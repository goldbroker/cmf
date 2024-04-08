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

use Doctrine\ODM\PHPCR\ChildrenCollection;
use Sonata\BlockBundle\Block\BlockContext;
use Sonata\BlockBundle\Block\BlockRendererInterface;
use Symfony\Cmf\Bundle\BlockBundle\Block\ContainerBlockService;
use Symfony\Cmf\Bundle\BlockBundle\Doctrine\Phpcr\ContainerBlock;
use Symfony\Cmf\Bundle\BlockBundle\Doctrine\Phpcr\SimpleBlock;
use Symfony\Component\HttpFoundation\Response;
use Twig\Environment;

class ContainerBlockServiceTest extends \PHPUnit\Framework\TestCase
{
    private $twig;

    public function setUp(): void
    {
        $this->twig = $this->createMock(Environment::class);
    }

    public function testExecutionOfDisabledBlock()
    {
        $containerBlock = new ContainerBlock();
        $containerBlock->setEnabled(false);

        $blockRendererMock = $this->createMock(BlockRendererInterface::class);
        $blockRendererMock->expects($this->never())
            ->method('render');

        $containerBlockService = new ContainerBlockService($this->twig, $blockRendererMock);
        $containerBlockService->execute(new BlockContext($containerBlock));
    }

    public function testExecutionOfEnabledBlock()
    {
        $template = '@CmfBlock/Block/block_container.html.twig';

        $simpleBlock1 = new SimpleBlock();
        $simpleBlock1->setId(1);

        $simpleBlock2 = new SimpleBlock();
        $simpleBlock2->setId(2);

        $childrenCollectionMock = $this->createMock(ChildrenCollection::class);

        $containerBlock = new ContainerBlock('foo');
        $containerBlock->setEnabled(true);
        $containerBlock->setChildren($childrenCollectionMock);

        $settings = ['divisible_by' => 0, 'divisible_class' => '', 'child_class' => '', 'template' => $template];

        $blockContext = new BlockContext($containerBlock, $settings);

        $responseContent1 = 'Rendered Simple Block 1.';
        $responseContent2 = 'Rendered Simple Block 2.';

        $blockRendererMock = $this->createMock(BlockRendererInterface::class);

        $this->twig
            ->expects($this->once())
            ->method('render')
            ->with(
                $this->equalTo($template),
                $this->equalTo([
                    'block' => $containerBlock,
                    'settings' => $settings,
                ])
            )
            ->will($this->returnValue(new Response($responseContent1.$responseContent2)))
        ;

        $containerBlockService = new ContainerBlockService($this->twig, $blockRendererMock);
        $response = $containerBlockService->execute($blockContext);
        $this->assertStringContainsString(($responseContent1.$responseContent2), $response->getContent());
    }

    public function testExecutionOfBlockWithNoChildren()
    {
        $template = '@CmfBlock/Block/block_container.html.twig';

        $childrenCollectionMock = $this->createMock(ChildrenCollection::class);

        $containerBlock = new ContainerBlock('foo');
        $containerBlock->setEnabled(true);
        $containerBlock->setChildren($childrenCollectionMock);

        $settings = ['divisibleBy' => 0, 'divisibleClass' => '', 'childClass' => '', 'template' => $template];

        $blockContext = new BlockContext($containerBlock, $settings);

        $blockRendererMock = $this->createMock(BlockRendererInterface::class);

        $this->twig
            ->expects($this->once())
            ->method('render')
            ->with(
                $this->equalTo($template),
                $this->equalTo([
                    'block' => $containerBlock,
                    'settings' => $settings,
                ])
            )
            ->will($this->returnValue(new Response('')))
        ;

        $containerBlockService = new ContainerBlockService($this->twig, $blockRendererMock);
        $response = $containerBlockService->execute($blockContext);
        $this->assertStringContainsString('', $response->getContent());
    }
}
