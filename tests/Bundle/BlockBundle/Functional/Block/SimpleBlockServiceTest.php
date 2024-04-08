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
use Symfony\Cmf\Bundle\BlockBundle\Block\SimpleBlockService;
use Symfony\Cmf\Bundle\BlockBundle\Doctrine\Phpcr\SimpleBlock;
use Twig\Environment;

class SimpleBlockServiceTest extends \PHPUnit\Framework\TestCase
{
    private $twig;

    public function setUp(): void
    {
        $this->twig = $this->createMock(Environment::class);
    }

    public function testExecutionOfEnabledBlock()
    {
        $template = '@CmfBlock/Block/block_simple.html.twig';
        $simpleBlock = new SimpleBlock();
        $simpleBlock->setEnabled(true);
        $blockContext = new BlockContext($simpleBlock, ['template' => $template]);

        $this->twig
            ->expects($this->once())
            ->method('render')
            ->with(
                $this->equalTo($template),
                $this->equalTo([
                    'block' => $simpleBlock,
                ])
            );

        $simpleBlockService = new SimpleBlockService($this->twig, $template);
        $simpleBlockService->execute($blockContext);
    }

    public function testExecutionOfDisabledBlock()
    {
        $simpleBlock = new SimpleBlock();
        $simpleBlock->setEnabled(false);

        $this->twig
            ->expects($this->never())
            ->method('render');

        $simpleBlockService = new SimpleBlockService($this->twig);
        $simpleBlockService->execute(new BlockContext($simpleBlock));
    }
}
