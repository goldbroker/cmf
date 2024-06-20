<?php

/*
 * This file is part of the Symfony CMF package.
 *
 * (c) 2011-2017 Symfony CMF
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Cmf\Bundle\ContentBundle\Doctrine\Phpcr;

use Doctrine\ODM\PHPCR\HierarchyInterface;
use PHPCR\NodeInterface;
use Symfony\Cmf\Bundle\ContentBundle\Model\StaticContentBase as ModelStaticContentBase;

class StaticContentBase extends ModelStaticContentBase implements HierarchyInterface
{
    protected ?object $parent = null;
    protected ?string $name = null;
    protected NodeInterface $node;

    /**
     * {@inheritdoc}
     */
    public function setParentDocument($parent): HierarchyInterface
    {
        $this->parent = $parent;

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
     * @deprecated For BC with the PHPCR-ODM 1.4 HierarchyInterface
     * @see setParentDocument
     */
    public function setParent($parent): HierarchyInterface
    {
        @trigger_error('The '.__METHOD__.'() method is deprecated and will be removed in version 3.0. Use setParentDocument() instead.', E_USER_DEPRECATED);

        return $this->setParentDocument($parent);
    }

    /**
     * @deprecated For BC with the PHPCR-ODM 1.4 HierarchyInterface
     * @see getParentDocument
     */
    public function getParent(): ?object
    {
        @trigger_error('The '.__METHOD__.'() method is deprecated and will be removed in version 3.0. Use getParentDocument() instead.', E_USER_DEPRECATED);

        return $this->getParentDocument();
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name)
    {
        $this->name = $name;
    }

    /**
     * Get the underlying PHPCR node of this document.
     *
     * @return NodeInterface
     */
    public function getNode(): ?NodeInterface
    {
        return $this->node;
    }
}
