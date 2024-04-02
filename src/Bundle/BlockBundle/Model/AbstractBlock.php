<?php

/*
 * This file is part of the Symfony CMF package.
 *
 * (c) 2011-2017 Symfony CMF
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Cmf\Bundle\BlockBundle\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Sonata\BlockBundle\Model\BlockInterface;
use Symfony\Cmf\Bundle\CoreBundle\Model\ChildInterface;
use Symfony\Cmf\Bundle\CoreBundle\PublishWorkflow\PublishableInterface;
use Symfony\Cmf\Bundle\CoreBundle\PublishWorkflow\PublishTimePeriodInterface;

/**
 * Base class for all blocks - connects to Sonata Blocks.
 *
 * Parent handling: The BlockInterface defines a parent to link back to
 * a container block if there is one. getParent may only return BlockInterface
 * objects, while getParentObject may return any "parent" even if its not
 * in a block hierarchy.
 */
abstract class AbstractBlock implements BlockInterface, PublishableInterface, PublishTimePeriodInterface, ChildInterface
{
    /**
     * @var string
     */
    protected $id;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var object
     */
    protected $parentDocument;

    /**
     * @var int
     */
    protected $ttl = 86400;

    /**
     * @var array
     */
    protected $settings = [];

    /**
     * @var \DateTime
     */
    protected $createdAt;

    /**
     * @var \DateTime
     */
    protected $updatedAt;

    /**
     * @var bool whether this content is publishable
     */
    protected $publishable = true;

    /**
     * @var \DateTime|null publication start time
     */
    protected $publishStartDate;

    /**
     * @var \DateTime|null publication end time
     */
    protected $publishEndDate;

    /**
     * If you want your block model to be translated it has to implement TranslatableInterface
     * this code is just here to make your life easier.
     *
     * @var string
     */
    protected $locale;

    /**
     * @param string $src
     *
     * @return string
     */
    protected function dashify($src)
    {
        return preg_replace('/[\/\.]/', '-', $src);
    }

    /**
     * {@inheritdoc}
     */
    public function setId($id): void
    {
        $this->id = $id;
    }

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * {@inheritdoc}
     */
    public function setType($type): void
    {
    }

    /**
     * {@inheritdoc}
     */
    public function setEnabled($enabled): void
    {
        $this->setPublishable($enabled);
    }

    /**
     * {@inheritdoc}
     */
    public function getEnabled(): bool
    {
        return $this->isPublishable();
    }

    /**
     * {@inheritdoc}
     */
    public function setPosition($position): void
    {
        // TODO: implement. https://github.com/symfony-cmf/BlockBundle/issues/150
    }

    /**
     * {@inheritdoc}
     */
    public function getPosition(): ?int
    {
        $siblings = $this->getParentObject()->getChildren();

        return array_search($siblings->indexOf($this), $siblings->getKeys());
    }

    /**
     * {@inheritdoc}
     */
    public function setCreatedAt(\DateTime $createdAt = null): void
    {
        $this->createdAt = $createdAt;
    }

    /**
     * {@inheritdoc}
     */
    public function getCreatedAt(): ?\DateTime
    {
        return $this->createdAt;
    }

    /**
     * {@inheritdoc}
     */
    public function setUpdatedAt(\DateTime $updatedAt = null): void
    {
        $this->updatedAt = $updatedAt;
    }

    /**
     * {@inheritdoc}
     */
    public function getUpdatedAt(): ?\DateTime
    {
        return $this->updatedAt;
    }

    /**
     * {@inheritdoc}
     */
    public function setPublishable($publishable)
    {
        return $this->publishable = (bool) $publishable;
    }

    /**
     * {@inheritdoc}
     */
    public function isPublishable()
    {
        return $this->publishable;
    }

    /**
     * {@inheritdoc}
     */
    public function getPublishStartDate()
    {
        return $this->publishStartDate;
    }

    /**
     * {@inheritdoc}
     */
    public function setPublishStartDate(\DateTime $publishStartDate = null)
    {
        $this->publishStartDate = $publishStartDate;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getPublishEndDate()
    {
        return $this->publishEndDate;
    }

    /**
     * {@inheritdoc}
     */
    public function setPublishEndDate(\DateTime $publishEndDate = null)
    {
        $this->publishEndDate = $publishEndDate;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function addChildren(BlockInterface $children): void
    {
    }

    /**
     * {@inheritdoc}
     */
    public function getChildren()
    {
        return new ArrayCollection();
    }

    /**
     * {@inheritdoc}
     */
    public function hasChildren(): bool
    {
        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function setName($name): void
    {
        $this->name = $name;
    }

    /**
     * {@inheritdoc}
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * Set parent object regardless of its type. This can be a ContainerBlock
     * but also any other object.
     *
     * {@inheritdoc}
     */
    public function setParentObject($parent)
    {
        $this->parentDocument = $parent;

        return $this;
    }

    /**
     * Get the parent object regardless of its type.
     *
     * {@inheritdoc}
     */
    public function getParentObject()
    {
        return $this->parentDocument;
    }

    /**
     * {@inheritdoc}
     *
     * Redirect to setParentObject
     */
    public function setParent(BlockInterface $parent = null): void
    {
        $this->setParentObject($parent);
    }

    /**
     * {@inheritdoc}
     *
     * Check if getParentObject is instanceof BlockInterface, otherwise return null
     */
    public function getParent(): ?self
    {
        if (($parent = $this->getParentObject()) instanceof BlockInterface) {
            return $parent;
        }

        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function hasParent(): bool
    {
        return $this->getParentObject() instanceof BlockInterface;
    }

    /**
     * Set ttl.
     *
     * @param int $ttl
     *
     * @return $this
     */
    public function setTtl($ttl)
    {
        $this->ttl = $ttl;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getTtl(): int
    {
        return $this->ttl;
    }

    /**
     * toString ...
     *
     * @return string
     */
    public function __toString()
    {
        return (string) $this->name;
    }

    /**
     * {@inheritdoc}
     */
    public function setSettings(array $settings = []): void
    {
        $this->settings = $settings;
    }

    /**
     * {@inheritdoc}
     */
    public function getSettings(): array
    {
        return $this->settings;
    }

    /**
     * {@inheritdoc}
     */
    public function setSetting($name, $value): void
    {
        $this->settings[$name] = $value;
    }

    /**
     * {@inheritdoc}
     */
    public function getSetting($name, $default = null)
    {
        return isset($this->settings[$name]) ? $this->settings[$name] : $default;
    }

    /**
     * @return string
     */
    public function getDashifiedId()
    {
        return $this->dashify($this->id);
    }

    /**
     * @return string
     */
    public function getDashifiedType()
    {
        return $this->dashify($this->getType());
    }

    /**
     * If you want your block model to be translated it has to implement
     * TranslatableInterface. This code is just here to make your life easier.
     *
     * @see TranslatableInterface::getLocale()
     */
    public function getLocale()
    {
        return $this->locale;
    }

    /**
     * If you want your block model to be translated it has to implement
     * TranslatableInterface. This code is just here to make your life easier.
     *
     * @see TranslatableInterface::setLocale()
     *
     * @param string $locale
     *
     * @return $this
     */
    public function setLocale($locale)
    {
        $this->locale = $locale;

        return $this;
    }
}
