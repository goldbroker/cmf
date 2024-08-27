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
    protected ?string $id = null;
    protected ?string $name = null;
    protected ?object $parentDocument = null;
    protected int $ttl = 86400;
    protected array $settings = [];
    protected ?\DateTime $createdAt = null;
    protected ?\DateTime $updatedAt = null;
    protected bool $publishable = true;
    protected ?\DateTime $publishStartDate = null;
    protected ?\DateTime $publishEndDate = null;

    /**
     * If you want your block model to be translated it has to implement TranslatableInterface
     * this code is just here to make your life easier.
     */
    protected ?string $locale = null;

    protected function dashify(string $src): string
    {
        return preg_replace('/[\/.]/', '-', $src);
    }

    /**
     * @param string $id
     */
    public function setId($id): void
    {
        $this->id = $id;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function setType(string $type): void
    {
    }

    public function setEnabled(bool $enabled): void
    {
        $this->setPublishable($enabled);
    }

    public function getEnabled(): bool
    {
        return $this->isPublishable();
    }

    public function setPosition(int $position): void
    {
        // TODO: implement. https://github.com/symfony-cmf/BlockBundle/issues/150
    }

    public function getPosition(): ?int
    {
        $siblings = $this->getParentObject()->getChildren();

        return array_search($siblings->indexOf($this), $siblings->getKeys());
    }

    public function setCreatedAt(?\DateTime $createdAt = null): void
    {
        $this->createdAt = $createdAt;
    }

    public function getCreatedAt(): ?\DateTime
    {
        return $this->createdAt;
    }

    public function setUpdatedAt(?\DateTime $updatedAt = null): void
    {
        $this->updatedAt = $updatedAt;
    }

    public function getUpdatedAt(): ?\DateTime
    {
        return $this->updatedAt;
    }

    /**
     * {@inheritdoc}
     */
    public function setPublishable(bool $publishable): void
    {
        $this->publishable = $publishable;
    }

    /**
     * {@inheritdoc}
     */
    public function isPublishable(): bool
    {
        return $this->publishable;
    }

    /**
     * {@inheritdoc}
     */
    public function getPublishStartDate(): ?\DateTime
    {
        return $this->publishStartDate;
    }

    /**
     * {@inheritdoc}
     */
    public function setPublishStartDate(?\DateTime $publishStartDate = null): AbstractBlock
    {
        $this->publishStartDate = $publishStartDate;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getPublishEndDate(): ?\DateTime
    {
        return $this->publishEndDate;
    }

    /**
     * {@inheritdoc}
     */
    public function setPublishEndDate(\DateTime $publishEndDate = null): AbstractBlock
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
    public function setName(?string $name): void
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
    public function getParentObject(): ?object
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
    public function getParent(): ?BlockInterface
    {
        $parent = $this->getParentObject();

        return $parent instanceof BlockInterface ? $parent : null;
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
    public function getLocale(): ?string
    {
        return $this->locale;
    }

    /**
     * If you want your block model to be translated it has to implement
     * TranslatableInterface. This code is just here to make your life easier.
     *
     * @see TranslatableInterface::setLocale()
     */
    public function setLocale(?string $locale): void
    {
        $this->locale = $locale;
    }
}
