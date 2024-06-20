<?php

/*
 * This file is part of the Symfony CMF package.
 *
 * (c) 2011-2017 Symfony CMF
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Cmf\Bundle\BlockBundle\Doctrine\Phpcr;

use Symfony\Cmf\Bundle\BlockBundle\Doctrine\Phpcr\ContainerBlock;
use Symfony\Cmf\Bundle\CoreBundle\Translatable\TranslatableInterface;

/**
 * Special container block that renders child items in a way suitable for a
 * slideshow. Note that you need to add some javascript to actually get the
 * blocks to do a slideshow.
 */
class SlideshowBlock extends ContainerBlock implements TranslatableInterface
{
    protected ?string $title = null;

    public function getType(): string
    {
        return 'cmf.block.slideshow';
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): SlideshowBlock
    {
        $this->title = $title;

        return $this;
    }
}
