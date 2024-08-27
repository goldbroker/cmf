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

use Sonata\BlockBundle\Model\BlockInterface;
use Symfony\Cmf\Bundle\BlockBundle\Doctrine\Phpcr\AbstractBlock;

/**
 * Block that is a reference to another block.
 */
class ReferenceBlock extends AbstractBlock
{
    private ?BlockInterface $referencedBlock = null;

    public function getType(): string
    {
        return 'cmf.block.reference';
    }

    public function getReferencedBlock(): ?BlockInterface
    {
        return $this->referencedBlock;
    }

    public function setReferencedBlock(BlockInterface $referencedBlock): ReferenceBlock
    {
        $this->referencedBlock = $referencedBlock;

        return $this;
    }
}
