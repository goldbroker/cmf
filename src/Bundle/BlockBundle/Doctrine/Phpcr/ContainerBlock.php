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

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ODM\PHPCR\ChildrenCollection;
use Sonata\BlockBundle\Model\BlockInterface;
use Symfony\Cmf\Bundle\BlockBundle\Doctrine\Phpcr\AbstractBlock;

/**
 * Block that contains other blocks.
 */
class ContainerBlock extends AbstractBlock
{
    /**
     * @var ChildrenCollection
     */
    protected $children;

    public function __construct(?string $name = null)
    {
        $this->setName($name);
        $this->children = new ArrayCollection();
    }

    /**
     * {@inheritdoc}
     */
    public function getType(): string
    {
        return 'cmf.block.container';
    }

    /**
     * Get children.
     *
     * @return ArrayCollection|ChildrenCollection
     */
    public function getChildren()
    {
        return $this->children;
    }

    /**
     * Set children.
     *
     * @param ChildrenCollection $children
     *
     * @return ChildrenCollection
     */
    public function setChildren(ChildrenCollection $children): ChildrenCollection
    {
        return $this->children = $children;
    }

    /**
     * Add a child to this container.
     */
    public function addChild(BlockInterface $child, string $key = null): void
    {
        if (null !== $key) {
            $this->children->set($key, $child);

            return;
        }

        $this->children->add($child);
    }

    /**
     * Alias to addChild to make the form layer happy.
     *
     * @param BlockInterface $children
     */
    public function addChildren(BlockInterface $children): void
    {
        $this->addChild($children);
    }

    /**
     * Remove a child from this container.
     *
     * @param BlockInterface $child
     *
     * @return $this
     */
    public function removeChild($child): ContainerBlock
    {
        $this->children->removeElement($child);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function hasChildren(): bool
    {
        return count($this->children) > 0;
    }
}
