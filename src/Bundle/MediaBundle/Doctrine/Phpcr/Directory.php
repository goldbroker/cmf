<?php

/*
 * This file is part of the Symfony CMF package.
 *
 * (c) 2011-2017 Symfony CMF
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Cmf\Bundle\MediaBundle\Doctrine\Phpcr;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ODM\PHPCR\Document\AbstractFile;
use Doctrine\ODM\PHPCR\Document\Folder;
use Symfony\Cmf\Bundle\MediaBundle\DirectoryInterface;

class Directory extends Folder implements DirectoryInterface
{
    protected ?\DateTimeInterface $updatedAt = null;

    protected ?string $updatedBy = null;

    public function __construct()
    {
        $this->children = new ArrayCollection();
    }

    /**
     * {@inheritdoc}
     */
    public function getName(): string
    {
        return $this->nodename;
    }

    /**
     * {@inheritdoc}
     */
    public function setName($name): self
    {
        $this->nodename = $name;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getParent(): ?object
    {
        return $this->parent;
    }

    /**
     * {@inheritdoc}
     */
    public function setParentDocument($parent): self
    {
        $this->parent = $parent;

        if ($parent instanceof self) {
            $parent->addChild($this);
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setParent($parent): self
    {
        return $this->setParentDocument($parent);
    }

    /**
     * {@inheritdoc}
     */
    public function getCreatedAt(): \DateTimeInterface
    {
        return $this->created;
    }

    /**
     * {@inheritdoc}
     */
    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    /**
     * The createdBy is assigned by the content repository
     * This is the name of the (jcr) user that updated the node.
     *
     * @return string name of the (jcr) user who updated the file
     */
    public function getUpdatedBy(): ?string
    {
        return $this->updatedBy;
    }
}
