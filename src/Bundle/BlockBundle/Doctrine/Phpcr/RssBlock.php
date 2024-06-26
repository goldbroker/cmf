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

use Symfony\Cmf\Bundle\BlockBundle\Doctrine\Phpcr\ActionBlock;

/**
 * Block to display a list of rss items.
 */
class RssBlock extends ActionBlock
{
    /**
     * {@inheritdoc}
     */
    public function getType(): string
    {
        return 'cmf.block.action';
    }

    /**
     * Returns the default action name.
     *
     * @return string
     */
    public function getDefaultActionName()
    {
        return 'cmf.block.rss_controller:block';
    }
}
