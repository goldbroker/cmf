<?php

/*
 * This file is part of the Symfony CMF package.
 *
 * (c) 2011-2017 Symfony CMF
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Cmf\Component\Resource\Puli\Api;

use Symfony\Cmf\Component\Puli\Api\Traversable;

/**
 * A collection of {@link PuliResource} instances.
 *
 * @since  1.0
 *
 * @author Bernhard Schussek <bschussek@gmail.com>
 */
interface ResourceCollection extends \Traversable, \ArrayAccess, \Countable
{
    /**
     * Adds a resource to the collection.
     *
     * @param PuliResource $resource the added resource
     */
    public function add(PuliResource $resource);

    /**
     * Sets a resource at a collection key.
     *
     * @param int          $key      the collection key
     * @param PuliResource $resource the resource to set
     */
    public function set($key, PuliResource $resource);

    /**
     * Returns the resource for a collection key.
     *
     * @param int $key the collection key
     *
     * @return PuliResource the resource at the key
     *
     * @throws \OutOfBoundsException if the key does not exist
     */
    public function get($key);

    /**
     * Removes a collection key from the collection.
     *
     * @param int $key the collection key
     */
    public function remove($key);

    /**
     * Returns whether a collection key exists.
     *
     * @param int $key the collection key
     *
     * @return bool whether the collection key exists
     */
    public function has($key);

    /**
     * Removes all resources from the collection.
     */
    public function clear();

    /**
     * Returns the keys of the collection.
     *
     * @return int[] the collection keys
     */
    public function keys();

    /**
     * Replaces the collection contents with the given resources.
     *
     * @param PuliResource[]|Traversable $resources the resources to write into the collection
     *
     * @throws \InvalidArgumentException if the resources are not an array and not a traversable object
     * @throws \RuntimeException         if a resource does not implement {@link PuliResource}
     */
    public function replace($resources);

    /**
     * Merges the given resources into the collection.
     *
     * @param PuliResource[]|Traversable $resources the resources to merge into the collection
     *
     * @throws \InvalidArgumentException if the resources are not an array and not a traversable object
     * @throws \RuntimeException         if a resource does not implement {@link PuliResource}
     */
    public function merge($resources);

    /**
     * Returns whether the collection is empty.
     *
     * @return bool returns `true` only if the collection contains no resources
     */
    public function isEmpty();

    /**
     * Returns the paths of all resources in the collection.
     *
     * The paths are returned in the order of their resources in the collection.
     *
     * @return string[] the paths of the resources in the collection
     *
     * @see PuliResource::getPath
     */
    public function getPaths();

    /**
     * Returns the names of all resources in the collection.
     *
     * The names are returned in the order of their resources in the collection.
     *
     * @return string[] the names of the resources in the collection
     *
     * @see PuliResource::getName
     */
    public function getNames();

    /**
     * Returns the collection contents as array.
     *
     * @return PuliResource[] the resources in the collection
     */
    public function toArray();
}
