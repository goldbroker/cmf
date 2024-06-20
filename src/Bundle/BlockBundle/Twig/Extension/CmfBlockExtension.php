<?php

/*
 * This file is part of the Symfony CMF package.
 *
 * (c) 2011-2017 Symfony CMF
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Cmf\Bundle\BlockBundle\Twig\Extension;

use Symfony\Cmf\Bundle\BlockBundle\Templating\Helper\CmfBlockHelper;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

/**
 * Utility function for blocks.
 *
 * @author David Buchmann <david@liip.ch>
 */
class CmfBlockExtension extends AbstractExtension
{
    protected CmfBlockHelper $blockHelper;

    public function __construct(CmfBlockHelper $blockHelper)
    {
        $this->blockHelper = $blockHelper;
    }

    public function getFilters(): array
    {
        return [
            new TwigFilter(
                'cmf_embed_blocks',
                [$this->blockHelper, 'embedBlocks'],
                ['is_safe' => ['html']]
            ),
        ];
    }

    public function getName(): string
    {
        return 'cmf_block';
    }
}
