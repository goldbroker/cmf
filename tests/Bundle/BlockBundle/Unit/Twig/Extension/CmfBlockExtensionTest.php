<?php

/*
 * This file is part of the Symfony CMF package.
 *
 * (c) 2011-2017 Symfony CMF
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tests\Symfony\Cmf\Bundle\BlockBundle\Unit\Twig\Extension;

use Symfony\Cmf\Bundle\BlockBundle\Templating\Helper\CmfBlockHelper;
use Symfony\Cmf\Bundle\BlockBundle\Twig\Extension\CmfBlockExtension;

class CmfBlockExtensionTest extends \PHPUnit\Framework\TestCase
{
    private $blockHelper;

    protected function setUp(): void
    {
        if (!class_exists('Twig_Environment')) {
            $this->markTestSkipped('Twig is not available.');
        }
    }

    /**
     * @dataProvider getEmbedFilterData
     */
    public function testEmbedFilter($template, $calls = 1)
    {
        $twig = new \Twig_Environment(new \Twig_Loader_Array([]), ['debug' => true, 'cache' => false, 'autoescape' => 'html', 'optimizations' => 0]);
        $twig->addExtension(new CmfBlockExtension($this->getBlockHelper()));

        $this->getBlockHelper()->expects($this->exactly($calls))
            ->method('embedBlocks');

        try {
            $twig->createTemplate($template)->render([]);
        } catch (\Twig_Error_Runtime $e) {
            throw $e->getPrevious();
        }
    }

    public function getEmbedFilterData()
    {
        return [
            ['{{ "bar"|cmf_embed_blocks }}'],
            ['{{ "bar"|cmf_embed_blocks }} lorem ipsum {{ "foo"|cmf_embed_blocks }}', 2],
        ];
    }

    protected function getBlockHelper()
    {
        if (null === $this->blockHelper) {
            $this->blockHelper = $this->createMock(CmfBlockHelper::class);
        }

        return $this->blockHelper;
    }
}
