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

use Doctrine\ODM\PHPCR\HierarchyInterface as PhpcrHierarchyInterface;
use Symfony\Cmf\Bundle\MediaBundle\HierarchyInterface;
use Symfony\Cmf\Bundle\MediaBundle\Model\AbstractMedia as ModelAbstractMedia;

abstract class AbstractMedia extends ModelAbstractMedia implements HierarchyInterface, PhpcrHierarchyInterface
{
    protected ?object $parent = null;

    protected ?string $createdBy = null;

    /**
     * {@inheritdoc}
     */
    public function setParentDocument($parent): self
    {
        $this->parent = $parent;

        if ($parent instanceof Directory) {
            $parent->addChild($this);
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getParentDocument(): ?object
    {
        return $this->parent;
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
    public function getParent(): ?object
    {
        return $this->getParentDocument();
    }

    /**
     * The createdBy is assigned by the content repository
     * This is the name of the (jcr) user that created the node.
     *
     * @return string name of the (jcr) user who created the file
     */
    public function getCreatedBy(): ?string
    {
        return $this->createdBy;
    }
}
