<?php

/*
 * This file is part of the Symfony CMF package.
 *
 * (c) 2011-2017 Symfony CMF
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tests\Symfony\Cmf\Bundle\BlockBundle\Functional\Block;

use Sonata\BlockBundle\Block\BlockContext;
use Symfony\Cmf\Bundle\BlockBundle\Block\MenuBlockService;
use Symfony\Cmf\Bundle\BlockBundle\Doctrine\Phpcr\MenuBlock;
use Symfony\Cmf\Bundle\MenuBundle\Doctrine\Phpcr\MenuNode;
use Twig\Environment;

class MenuBlockServiceTest extends \PHPUnit\Framework\TestCase
{
    private $twig;

    public function setUp(): void
    {
        $this->twig = $this->createMock(Environment::class);
    }

    public function testExecutionOfDisabledBlock()
    {
        $menuBlock = new MenuBlock();
        $menuBlock->setEnabled(false);

        $menuBlockService = new MenuBlockService($this->twig);
        $menuBlockService->execute(new BlockContext($menuBlock));
    }

    public function testExecutionOfEnabledBlock()
    {
        $template = '@CmfBlock/Block/block_menu.html.twig';
        $menuNode = new MenuNode();

        $menuBlock = new MenuBlock();
        $menuBlock->setEnabled(true);
        $menuBlock->setMenuNode($menuNode);

        $menuBlockContext = new BlockContext($menuBlock, ['template' => $template]);

        $menuBlockService = new MenuBlockService($this->twig);
        $menuBlockService->execute($menuBlockContext);
    }

//    public function testSetMenuNode()
//    {
//        $menuBlock = new MenuBlock();
//        $this->assertAttributeEmpty('menuNode', $menuBlock);
//
//        $menuBlock->setMenuNode($this->createMock(NodeInterface::class));
//        $this->assertAttributeInstanceOf(NodeInterface::class, 'menuNode', $menuBlock);
//
//        $menuBlock->setMenuNode(null);
//        $this->assertAttributeSame(null, 'menuNode', $menuBlock);
//    }
}
