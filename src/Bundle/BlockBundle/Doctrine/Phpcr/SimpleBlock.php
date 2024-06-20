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

use Symfony\Cmf\Bundle\BlockBundle\Doctrine\Phpcr\AbstractBlock;
use Symfony\Cmf\Bundle\CoreBundle\Translatable\TranslatableInterface;

/**
 * Block that contains hypertext and a title.
 */
class SimpleBlock extends AbstractBlock implements TranslatableInterface
{
    protected ?string $title = null;

    protected ?string $body = null;

    public function getType(): string
    {
        return 'cmf.block.simple';
    }

    public function setTitle(string $title): SimpleBlock
    {
        $this->title = $title;

        return $this;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setBody(string $body): SimpleBlock
    {
        $this->body = $body;

        return $this;
    }

    public function getBody(): ?string
    {
        return $this->body;
    }
}
