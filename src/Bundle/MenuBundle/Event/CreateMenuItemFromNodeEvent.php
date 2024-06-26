<?php

namespace Symfony\Cmf\Bundle\MenuBundle\Event;

use Knp\Menu\ItemInterface;
use Knp\Menu\NodeInterface;
use Symfony\Contracts\EventDispatcher\Event;

/**
 * This event is raised when a menu node is to be transformed into a menu item.
 *
 * The event allows to control whether the menu node should be handled or to
 * completely replace the default behaviour of converting a menu node to a menu
 * item.
 *
 * @author Ben Glassman <bglassman@gmail.com>
 */
class CreateMenuItemFromNodeEvent extends Event
{
    private NodeInterface $node;

    private ?ItemInterface $item = null;

    /**
     * Whether or not to skip processing of this node.
     */
    private bool $skipNode = false;

    /**
     * Whether or not to skip processing of child nodes.
     */
    private bool $skipChildren = false;

    public function __construct(NodeInterface $node)
    {
        $this->node = $node;
    }

    /**
     * Get the menu node that is about to be built.
     *
     * @return NodeInterface
     */
    public function getNode(): NodeInterface
    {
        return $this->node;
    }

    /**
     * Get the menu item attached to this event.
     *
     * If this is non-null, it will be used instead of automatically converting
     * the NodeInterface into a MenuItem.
     *
     * @return ItemInterface
     */
    public function getItem(): ?ItemInterface
    {
        return $this->item;
    }

    /**
     * Set the menu item that represents the menu node of this event.
     *
     * Unless you set the skip children option, the children from the menu node
     * will still be built and added after eventual children this menu item
     * has.
     *
     * @param ?ItemInterface $item Menu item to use
     */
    public function setItem(?ItemInterface $item = null): void
    {
        $this->item = $item;
    }

    /**
     * Set whether the node associated with this event is to be skipped
     * entirely. This has precedence over an eventual menu item attached to the
     * event.
     *
     * This automatically skips the whole subtree, as the children have no
     * place where they could be attached to.
     *
     * @param bool $skipNode
     */
    public function setSkipNode(bool $skipNode): void
    {
        $this->skipNode = $skipNode;
    }

    /**
     * @return bool Whether the node associated to this event is to be skipped
     */
    public function isSkipNode(): bool
    {
        return $this->skipNode;
    }

    /**
     * Set whether the children of the *node* associated with this event should
     * be ignored.
     *
     * Use this for example when your event handler implements its own logic to
     * build children items for the node associated with this event.
     *
     * If this event has a menu *item*, those children won't be skipped.
     *
     * @param bool $skipChildren
     */
    public function setSkipChildren(bool $skipChildren): void
    {
        $this->skipChildren = $skipChildren;
    }

    /**
     * @return bool Whether the children of the node associated to this event
     *              should be handled or ignored
     */
    public function isSkipChildren(): bool
    {
        return $this->skipChildren;
    }
}
