<?php

/*
 * This file is part of the Symfony CMF package.
 *
 * (c) 2011-2017 Symfony CMF
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Cmf\Bundle\ContentBundle\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Knp\Menu\NodeInterface;
use Sonata\BlockBundle\Model\BlockInterface;
use Symfony\Cmf\Bundle\CoreBundle\PublishWorkflow\PublishableInterface;
use Symfony\Cmf\Bundle\CoreBundle\PublishWorkflow\PublishTimePeriodInterface;
use Symfony\Cmf\Bundle\CoreBundle\Translatable\TranslatableInterface;
use Symfony\Cmf\Bundle\MenuBundle\Model\MenuNode;
use Symfony\Cmf\Bundle\MenuBundle\Model\MenuNodeReferrersInterface;
use Symfony\Cmf\Component\Routing\RouteObjectInterface;
use Symfony\Cmf\Component\Routing\RouteReferrersInterface;

/**
 * Standard implementation of StaticContent:.
 *
 * Standard features:
 *
 * - Publish workflow
 * - Translatable
 * - RouteAware
 * - MenuAware
 *
 * Bundle specific:
 *
 * - Tags
 * - Additional Info Block
 */
class StaticContent extends StaticContentBase implements
    MenuNodeReferrersInterface,
    RouteReferrersInterface,
    PublishTimePeriodInterface,
    PublishableInterface,
    TranslatableInterface
{
    protected bool $publishable = true;
    protected ?\DateTime $publishStartDate = null;
    protected ?\DateTime $publishEndDate = null;
    protected ?string $locale = null;

    /**
     * @var Collection<RouteObjectInterface>
     */
    protected iterable $routes = [];

    /**
     * @var Collection<MenuNode>
     */
    protected ?Collection $menuNodes = null;

    /**
     * @var string[]
     */
    protected array $tags = [];

    /**
     * Hashmap for application data associated to this document. Both keys and
     * values must be strings.
     *
     * @var array
     */
    protected array $extras = [];

    /**
     * This will usually be a ContainerBlock but can be any block that will be
     * rendered in the additionalInfoBlock area.
     */
    protected ?BlockInterface $additionalInfoBlock = null;

    public function __construct()
    {
        $this->routes = new ArrayCollection();
        $this->menuNodes = new ArrayCollection();
    }

    /**
     * {@inheritdoc}
     */
    public function getLocale(): ?string
    {
        return $this->locale;
    }

    /**
     * {@inheritdoc}
     */
    public function setLocale(?string $locale): void
    {
        $this->locale = $locale;
    }

    /**
     * Get the tags set on this content.
     *
     * @return string[]
     */
    public function getTags(): array
    {
        return $this->tags;
    }

    /**
     * Set the tags of this content as an array of strings.
     *
     * @param string[] $tags
     */
    public function setTags(array $tags): void
    {
        $this->tags = $tags;
    }

    public function getAdditionalInfoBlock(): ?BlockInterface
    {
        return $this->additionalInfoBlock;
    }

    /**
     * Set the additional info block for this content. Usually you want this to
     * be a container block in order to be able to add several blocks.
     *
     * @param BlockInterface $block must be persistable through cascade by the
     *                              persistence layer
     */
    public function setAdditionalInfoBlock(BlockInterface $block): void
    {
        $this->additionalInfoBlock = $block;
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
    public function setPublishStartDate(\DateTime $publishStartDate = null)
    {
        $this->publishStartDate = $publishStartDate;
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
    public function setPublishEndDate(\DateTime $publishEndDate = null)
    {
        $this->publishEndDate = $publishEndDate;
    }

    /**
     * Get the application information associated with this document.
     *
     * @return array
     */
    public function getExtras(): array
    {
        return $this->extras;
    }

    /**
     * Get a single application information value.
     */
    public function getExtra(string $name, ?string $default = null): ?string
    {
        return $this->extras[$name] ?? $default;
    }

    /**
     * Set the application information.
     *
     * @return StaticContent - this instance
     */
    public function setExtras(array $extras): StaticContent
    {
        $this->extras = $extras;

        return $this;
    }

    /**
     * Set a single application information value.
     *
     * @param string $name
     * @param string $value the new value, null removes the entry
     *
     * @return StaticContent - this instance
     */
    public function setExtra(string $name, ?string $value = null): StaticContent
    {
        if (is_null($value)) {
            unset($this->extras[$name]);
        } else {
            $this->extras[$name] = $value;
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function addRoute($route): void
    {
        $this->routes->add($route);
    }

    /**
     * {@inheritdoc}
     */
    public function removeRoute($route): void
    {
        $this->routes->removeElement($route);
    }

    /**
     * {@inheritdoc}
     */
    public function getRoutes(): iterable
    {
        return $this->routes;
    }

    /**
     * {@inheritdoc}
     */
    public function addMenuNode(NodeInterface $menu)
    {
        $this->menuNodes->add($menu);
    }

    /**
     * {@inheritdoc}
     */
    public function removeMenuNode(NodeInterface $menu)
    {
        $this->menuNodes->removeElement($menu);
    }

    /**
     * {@inheritdoc}
     */
    public function getMenuNodes(): ?iterable
    {
        return $this->menuNodes;
    }
}
