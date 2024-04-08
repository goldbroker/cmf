<?php

/*
 * This file is part of the Symfony CMF package.
 *
 * (c) 2011-2017 Symfony CMF
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tests\Symfony\Cmf\Bundle\MenuBundle\Functional\Doctrine\Phpcr;

use Doctrine\ODM\PHPCR\Document\Generic;
use Doctrine\ODM\PHPCR\DocumentManager;
use Doctrine\ODM\PHPCR\Exception\OutOfBoundsException;
use Symfony\Cmf\Bundle\MenuBundle\Doctrine\Phpcr\Menu;
use Symfony\Cmf\Bundle\MenuBundle\Doctrine\Phpcr\MenuNode;
use Symfony\Cmf\Component\Testing\Functional\BaseTestCase;

class MenuTest extends BaseTestCase
{
    /**
     * @var DocumentManager
     */
    private $dm;

    private $rootDocument;

    public static function getKernelClass(): string
    {
        return \Tests\Symfony\Cmf\Bundle\MenuBundle\Fixtures\App\Kernel::class;
    }

    protected function setUp(): void
    {
        $this->db('PHPCR')->createTestNode();

        $this->dm = $this->db('PHPCR')->getOm();
        $this->rootDocument = $this->dm->find(null, '/test');
    }

    public function testPersist()
    {
        $menu = new Menu();
        $menu->setPosition($this->rootDocument, 'main');
        $this->dm->persist($menu);

        $menuNode = new MenuNode();
        $menuNode->setName('home');
        $menu->addChild($menuNode);
        $this->dm->persist($menuNode);

        $this->dm->flush();
        $this->dm->clear();

        $menu = $this->dm->find(null, '/test/main');

        $this->assertNotNull($menu);
        $this->assertEquals('main', $menu->getName());

        $children = $menu->getChildren();
        $this->assertCount(1, $children);
        $this->assertEquals('home', $children[0]->getName());
    }

    /**
     * @dataProvider getInvalidChildren
     */
    public function testPersistInvalidChild($invalidChild)
    {
        $this->expectException(OutOfBoundsException::class);
        $menu = new Menu();
        $menu->setPosition($this->rootDocument, 'main');
        $this->dm->persist($menu);

        $invalidChild->setParentDocument($menu);
        $this->dm->persist($invalidChild);

        $this->dm->flush();
    }

    public function getInvalidChildren()
    {
        return [
            [(new Menu())->setName('invalid')],
            [(new Generic())->setNodename('invalid')],
        ];
    }
}
