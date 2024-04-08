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
use Symfony\Cmf\Bundle\BlockBundle\Block\StringBlockService;
use Symfony\Cmf\Bundle\BlockBundle\Doctrine\Phpcr\StringBlock;
use Twig\Environment;

class StringBlockServiceTest extends \PHPUnit\Framework\TestCase
{
    private $twig;

    public function setUp(): void
    {
        $this->twig = $this->createMock(Environment::class);
    }

    public function testExecutionOfEnabledBlock()
    {
        $template = '@CmfBlock/Block/block_string.html.twig';
        $stringBlock = new StringBlock();
        $stringBlock->setEnabled(true);
        $blockContext = new BlockContext($stringBlock, ['template' => $template]);

        $this->twig
            ->expects($this->once())
            ->method('render')
            ->with(
                $this->equalTo($template),
                $this->equalTo([
                    'block' => $stringBlock,
                ])
            );

        $stringBlockService = new StringBlockService($this->twig, $template);
        $stringBlockService->execute($blockContext);
    }

    public function testExecutionOfDisabledBlock()
    {
        $stringBlock = new StringBlock();
        $stringBlock->setEnabled(false);

        $this->twig
            ->expects($this->never())
            ->method('render');

        $stringBlockService = new StringBlockService($this->twig);
        $stringBlockService->execute(new BlockContext($stringBlock));
    }
}
