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

use PHPCR\NodeInterface;
use Symfony\Cmf\Component\Resource\Puli\ArrayResourceCollection;
use Symfony\Cmf\Component\Resource\Repository\PhpcrRepository;
use Symfony\Cmf\Component\Resource\Repository\Resource\PhpcrResource;

class PhpcrRepositoryTest extends AbstractPhpcrRepositoryTestCase
{
    protected $node;

    protected $node1;

    protected $node2;

    public function setUp(): void
    {
        parent::setUp();
        $this->node = $this->createMock(NodeInterface::class);
        $this->node1 = $this->createMock(NodeInterface::class);
        $this->node2 = $this->createMock(NodeInterface::class);
    }

    /**
     * {@inheritdoc}
     *
     * @dataProvider provideGet
     */
    public function testGet($basePath, $requestedPath, $canonicalPath, $evaluatedPath)
    {
        $this->session->method('getNode')->with($evaluatedPath)->willReturn($this->node);

        $this->node->method('getPath')->willReturn($evaluatedPath);

        $res = $this->getRepository($basePath)->get($requestedPath);

        $this->assertInstanceOf(PhpcrResource::class, $res);

        $this->assertEquals($requestedPath, $res->getPath());
        $this->assertEquals('foobar', $res->getName());
        $this->assertSame($this->node, $res->getPayload());
        $this->assertTrue($res->isAttached());
    }

    /**
     * {@inheritdoc}
     */
    public function testFind()
    {
        $this->session->method('getNode')->with('/cmf/foobar')->willReturn($this->node);
        $this->finder->method('find')->with('/cmf/*')->willReturn([
            $this->node,
        ]);

        $res = $this->getRepository()->find('/cmf/*');

        $this->assertInstanceOf(ArrayResourceCollection::class, $res);
        $this->assertCount(1, $res);
        $nodeResource = $res->offsetGet(0);
        $this->assertSame($this->node, $nodeResource->getPayload());
    }

    /**
     * {@inheritdoc}
     *
     * @dataProvider provideGet
     */
    public function testListChildren($basePath, $requestedPath, $canonicalPath, $absPath)
    {
        $this->session->method('getNode')->with($absPath)->willReturn($this->node);
        $this->node->method('getNodes')->willReturn([
            $this->node1, $this->node2,
        ]);
        $this->node1->method('getPath')->willReturn($absPath.'/node1');
        $this->node2->method('getPath')->willReturn($absPath.'/node2');

        $res = $this->getRepository($basePath)->listChildren($requestedPath);

        $this->assertInstanceOf(ArrayResourceCollection::class, $res);
        $this->assertCount(2, $res);
        $this->assertInstanceOf(PhpcrResource::class, $res[0]);
        $this->assertEquals($canonicalPath.'/node1', $res[0]->getPath());
    }

    public function testGetNotExisting()
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('No PHPCR node could be found at "/test"');

        $this->session->method('getNode')->with('/test')->will($this->throwException(new \PHPCR\PathNotFoundException()));
        $this->getRepository()->get('/test');
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
            $node = $this->createMock(NodeInterface::class);
            $node->method('getPath')->willReturn('/test');
            $children[] = $node;
        }

        $this->session->method('getNode')->with('/test')->willReturn($this->node);
        $this->node->method('getNodes')->willReturn($children);

        $res = $this->getRepository()->hasChildren('/test');

        $this->assertEquals($hasChildren, $res);
    }

    /**
     * {@inheritdoc}
     */
    public function testRemove()
    {
        $this->finder->method('find')->with('/test/*')->willReturn([
            $this->node1,
            $this->node2,
        ]);
        $this->node1->method('getPath')->willReturn('/test/path1');
        $this->node2->method('getPath')->willReturn('/test/path2');

        $this->node1->expects($this->once())->method('remove');
        $this->node2->expects($this->once())->method('remove');
        $this->session->expects($this->once())->method('save');

        $this->getRepository()->remove('/test/*', 'glob');
    }

    /**
     * {@inheritdoc}
     */
    public function testRemoveException()
    {
        $this->finder->method('find')->with(null, '/test/path1')->willReturn([
            $this->node1,
        ]);
        $this->node1->method('remove')->will($this->throwException(new \InvalidArgumentException('test')));

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
    public function testMoveException()
    {
        $this->finder->method('find')->with(null, '/test/path1')->willReturn([
            $this->node1,
        ]);
        $this->node1->method('getPath')->willReturn('/test/path1');
        $this->node1->method('getName')->willReturn('path1');
        $this->node1->method('remove')->will($this->throwException(new \InvalidArgumentException('test')));

//        $this->expectException(\InvalidArgumentException::class);
//        $this->expectExceptionMessage('test');

        try {
            $nodes = $this->getRepository()->move('/test/path1', '/test/path2');
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
    public function testMove()
    {
        $this->finder->method('find')->with(null, '/test/path1')->willReturn([
            $this->node1,
        ]);
        $this->node1->method('getPath')->willReturn('/test/path1');
        $this->session->expects($this->once())->method('save');

        $this->getRepository()->move('/test/path1', '/foo/bar');
    }

    /**
     * {@inheritdoc}
     */
    public function testMoveMultiple()
    {
        $this->finder->method('find')->with('/test/*')->willReturn([
            $this->node1,
            $this->node2,
        ]);
        $this->node1->method('getPath')->willReturn('/test/path1');
        $this->node2->method('getPath')->willReturn('/test/path2');
        $this->node1->method('getName')->willReturn('path1');
        $this->node2->method('getName')->willReturn('path2');

        $this->session->expects($this->exactly(2))->method('move');
        $this->session->expects($this->once())->method('save');

        $this->getRepository()->move('/test/*', '/foo');
    }

    /**
     * {@inheritdoc}
     */
    public function testReorder()
    {
        $evaluatedPath = '/test/node-1';

        $this->session->method('getNode')->with($evaluatedPath)->willReturn($this->node);
        $this->node->method('getPath')->willReturn($evaluatedPath);
        $this->node->method('getParent')->willReturn($this->node1);
        $this->node->method('getName')->willReturn('node-1');
        $this->node1->method('getNodeNames')->willReturn(new \ArrayIterator([
            'node-1', 'node-2', 'node-3',
        ]));

        $this->node1->expects($this->once())->method('orderBefore')->with('node-1', 'node-3');
        $this->session->expects($this->once())->method('save');

        $this->getRepository('/test')->reorder('/node-1', 1);
    }

    /**
     * {@inheritdoc}
     */
    public function testReorderToLast()
    {
        $evaluatedPath = '/test/node-1';

        $this->session->method('getNode')->with($evaluatedPath)->willReturn($this->node);
        $this->node->method('getPath')->willReturn($evaluatedPath);
        $this->node->method('getParent')->willReturn($this->node1);
        $this->node->method('getName')->willReturn('node-1');
        $this->node1->method('getNodeNames')->willReturn([
            'node-1', 'node-2', 'node-3',
        ]);

        $this->node1->expects($this->exactly(2))->method('orderBefore')->withConsecutive(
            ['node-1', 'node-3'],
            ['node-3', 'node-1']
        );

        $this->session->expects($this->once())->method('save');

        $this->getRepository('/test')->reorder('/node-1', 66);
    }

    /**
     * {@inheritdoc}
     */
    protected function getRepository($path = null)
    {
        return new PhpcrRepository($this->session, $path, $this->finder);
    }
}
