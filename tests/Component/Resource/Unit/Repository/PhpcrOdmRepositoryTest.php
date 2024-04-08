<?php

/*
 * This file is part of the Symfony CMF package.
 *
 * (c) 2011-2017 Symfony CMF
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tests\Symfony\Cmf\Component\Resource\Unit\Repository;

use Doctrine\ODM\PHPCR\ChildrenCollection;
use Doctrine\ODM\PHPCR\DocumentManagerInterface;
use Doctrine\ODM\PHPCR\UnitOfWork;
use Doctrine\Persistence\ManagerRegistry;
use PHPCR\NodeInterface;
use Symfony\Cmf\Component\Resource\Puli\ArrayResourceCollection;
use Symfony\Cmf\Component\Resource\Repository\PhpcrOdmRepository;
use Symfony\Cmf\Component\Resource\Repository\Resource\PhpcrOdmResource;

class PhpcrOdmRepositoryTest extends AbstractPhpcrRepositoryTestCase
{
    private $documentManager;

    private $managerRegistry;

    private $childrenCollection;

    private $uow;

    private $document;

    private $child1;

    private $child2;

    private $node1;

    private $node2;

    public function setUp(): void
    {
        parent::setUp();
        $this->documentManager = $this->createMock(DocumentManagerInterface::class);
        $this->managerRegistry = $this->createMock(ManagerRegistry::class);
        $this->childrenCollection = $this->createMock(ChildrenCollection::class);
        $this->uow = $this->createMock(UnitOfWork::class);

        $this->document = new \stdClass();
        $this->child1 = new \stdClass();
        $this->child2 = new \stdClass();

        $this->node1 = $this->createMock(NodeInterface::class);
        $this->node2 = $this->createMock(NodeInterface::class);

        $this->managerRegistry->method('getManager')->willReturn($this->documentManager);
        $this->documentManager->method('getUnitOfWork')->willReturn($this->uow);
    }

    /**
     * {@inheritdoc}
     *
     * @dataProvider provideGet
     */
    public function testGet($basePath, $requestedPath, $canonicalPath, $evaluatedPath)
    {
        $this->documentManager->method('find')->with(null, $evaluatedPath)->willReturn($this->document);

        $res = $this->getRepository($basePath)->get($requestedPath);

        $this->assertInstanceOf('Symfony\Cmf\Component\Resource\Repository\Resource\PhpcrOdmResource', $res);
        $this->assertSame($this->document, $res->getPayload());
        $this->assertTrue($res->isAttached());
    }

    /**
     * {@inheritdoc}
     */
    public function testFind()
    {
        $this->documentManager->method('find')->with(null, '/base/path/cmf/foobar')->willReturn($this->document);
        $this->uow->method('getDocumentId')->with($this->document)->willReturn('/cmf/foobar');

        $this->finder->method('find')->with('/base/path/cmf/*')->willReturn([
            $this->document,
        ]);

        $res = $this->getRepository('/base/path')->find('/cmf/*');

        $this->assertInstanceOf(ArrayResourceCollection::class, $res);
        $this->assertCount(1, $res);
        $documentResource = $res->offsetGet(0);
        $this->assertSame($this->document, $documentResource->getPayload());
    }

    /**
     * {@inheritdoc}
     *
     * @dataProvider provideGet
     */
    public function testListChildren($basePath, $requestedPath, $canonicalPath, $absPath)
    {
        $this->documentManager->method('find')->with(null, $absPath)->willReturn($this->document);
        $this->childrenCollection->method('toArray')->willReturn([
            $this->child1,
            $this->child2
        ]);
        $this->documentManager->method('getChildren')->with($this->document)->willReturn($this->childrenCollection);
        $this->uow->method('getDocumentId')->with($this->child1)->willReturn($absPath.'/child1');
        $this->uow->method('getDocumentId')->with($this->child2)->willReturn($absPath.'/child2');

        $res = $this->getRepository($basePath)->listChildren($requestedPath);

        $this->assertInstanceOf(ArrayResourceCollection::class, $res);
        $this->assertCount(2, $res);
        $this->assertInstanceOf(PhpcrOdmResource::class, $res[0]);
        $this->assertEquals($canonicalPath.'/child1', $res[0]->getPath());
    }

    /**
     * {@inheritdoc}
     *
     * @dataProvider provideHasChildren
     */
    public function testHasChildren($nbChildren, $hasChildren)
    {
        $children = [];
        for ($i = 0; $i < $nbChildren; ++$i) {
            $children[] = new \stdClass();
        }

        $this->documentManager->method('getChildren')->with($this->document)->willReturn($this->childrenCollection);
        $this->childrenCollection->method('toArray')->willReturn($children);
        $this->documentManager->method('find')->with(null, '/test')->willReturn($this->document);
        $this->uow->method('getDocumentId')->willReturn('/test');

        $res = $this->getRepository()->hasChildren('/test');

        $this->assertEquals($hasChildren, $res);
    }

    public function testGetNotExisting()
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('No PHPCR-ODM document could be found at "/test"');

        $this->documentManager->method('find')->with(null, '/test')->willReturn(null);
        $this->getRepository()->get('/test');
    }

    /**
     * {@inheritdoc}
     */
    public function testRemove()
    {
        $this->finder->method('find')->with('/test/*')->willReturn([
            $this->child1,
            $this->child2,
        ]);

        $this->documentManager->expects($this->exactly(2))->method('remove')->withConsecutive([$this->child1], [$this->child2]);
        $this->documentManager->expects($this->once())->method('flush');

        $number = $this->getRepository()->remove('/test/*', 'glob');
        $this->assertEquals(2, $number);
    }

    /**
     * {@inheritdoc}
     */
    public function testRemoveException()
    {
        $this->finder->method('find')->with('/test/path1')->willReturn([
            $this->document,
        ]);
        $this->documentManager->method('remove')->with($this->document)->will($this->throwException(new \InvalidArgumentException('test')));

        try {
            $this->getRepository()->remove('/test/path1');
        } catch (\Exception $e) {
            $this->assertWrappedException(
                \RuntimeException::class,
                'Error encountered when removing resource(s) using query "/test/path1"',
                \InvalidArgumentException::class,
                'test',
                $e
            );
        }
    }

    /**
     * {@inheritdoc}
     */
    public function testMove()
    {
        $this->finder->method('find')->with('/test/path1')->willReturn([
            $this->document,
        ]);
        $this->documentManager->expects($this->once())->method('move')->with($this->document, '/foo/bar');
        $this->documentManager->expects($this->once())->method('flush');

        $number = $this->getRepository()->move('/test/path1', '/foo/bar');

        $this->assertEquals(1, $number);
    }

    /**
     * {@inheritdoc}
     */
    public function testMoveMultiple()
    {
        $this->finder->method('find')->with('/test/*')->willReturn([
            $this->child1,
            $this->child2,
        ]);

        $this->documentManager->method('getNodeForDocument')->with($this->child1)->willReturn($this->node1);
        $this->documentManager->method('getNodeForDocument')->with($this->child2)->willReturn($this->node2);
        $this->node1->method('getName')->willReturn('path1');
        $this->node2->method('getName')->willReturn('path2');

        $this->documentManager->expects($this->exactly(2))->method('move')->withConsecutive(
            [$this->child1, '/foo/path1'],
            [$this->child2, '/foo/path2']
        );
        $this->documentManager->expects($this->once())->method('flush');

        $number = $this->getRepository()->move('/test/*', '/foo');

        $this->assertEquals(2, $number);
    }

    /**
     * {@inheritdoc}
     */
    public function testMoveException()
    {
        $this->finder->method('find')->with('/test/path1')->willReturn([
            $this->document,
        ]);
        $this->documentManager->method('move')->with($this->document, '/test/path2')->will(
            $this->throwException(new \InvalidArgumentException('test'))
        );

        try {
            $this->getRepository()->move('/test/path1', '/test/path2');
        } catch (\Exception $e) {
            $this->assertWrappedException(
                \RuntimeException::class,
                'Error encountered when moving resource(s) using query "/test/path1"',
                \InvalidArgumentException::class,
                'test',
                $e
            );
        }
    }

    /**
     * {@inheritdoc}
     */
    public function testReorder()
    {
        $this->doTestReorder(1, true);
    }

    /**
     * {@inheritdoc}
     */
    public function testReorderToLast()
    {
        $this->doTestReorder(66, false);
    }

    private function doTestReorder($position, $before)
    {
        $evaluatedPath = '/test/foo';

        $this->documentManager->method('find')->with(null, $evaluatedPath)->willReturn($this->child1);
        $this->documentManager->method('getNodeForDocument')->with($this->child1)->willReturn($this->node1);
        $this->node1->method('getParent')->willReturn($this->node2);
        $this->node1->method('getName')->willReturn('foo');
        $this->node2->method('getNodeNames')->willReturn(new \ArrayIterator([
            'foo', 'bar', 'baz',
        ]));
        $this->node2->method('getPath')->willReturn('/test');
        $this->documentManager->method('find')->with(null, '/test')->willReturn($this->document);
        $this->documentManager->expects($this->once())->method('reorder')->with($this->document, 'foo', 'baz', $before);
        $this->documentManager->expects($this->once())->method('flush');

        $this->getRepository('/test')->reorder('/foo', $position);
    }

    protected function getRepository($path = null)
    {
        $repository = new PhpcrOdmRepository($this->managerRegistry, $path, $this->finder);

        return $repository;
    }
}

class stdClass2 extends \stdClass
{
}
