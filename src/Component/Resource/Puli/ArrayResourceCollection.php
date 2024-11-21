<?php

/*
 * This file is part of the Symfony CMF package.
 *
 * (c) 2011-2017 Symfony CMF
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Cmf\Component\Resource\Puli;

use Symfony\Cmf\Component\Resource\Puli\Api\PuliResource;
use Symfony\Cmf\Component\Resource\Puli\Api\ResourceCollection;
use Webmozart\Assert\Assert;

/**
 * A collection of {@link PuliResource} instances backed by an array.
 *
 * @since  1.0
 *
 * @author Bernhard Schussek <bschussek@gmail.com>
 */
class ArrayResourceCollection implements \IteratorAggregate, ResourceCollection
{
    /**
     * @var PuliResource[]|\Traversable
     */
    private $resources;

    /**
     * Creates a new collection.
     *
     * You can pass the resources that you want to initially store in the
     * collection as argument.
     *
     * @param PuliResource[]|\Traversable $resources the resources to store in the collection
     *
     * @throws \InvalidArgumentException if the resources are not an array and not a traversable object
     * @throws \RuntimeException         if a resource does not implement {@link PuliResource}
     */
    public function __construct($resources = [])
    {
        $this->replace($resources);
    }

    /**
     * {@inheritdoc}
     */
    public function add(PuliResource $resource): void
    {
        $this->resources[] = $resource;
    }

    /**
     * {@inheritdoc}
     */
    public function set($key, PuliResource $resource): void
    {
        $this->resources[$key] = $resource;
    }

    /**
     * {@inheritdoc}
     */
    public function get($key)
    {
        if (!isset($this->resources[$key])) {
            throw new \OutOfBoundsException(sprintf(
                'The offset "%s" does not exist.',
                $key
            ));
        }

        return $this->resources[$key];
    }

    /**
     * {@inheritdoc}
     */
    public function remove($key): void
    {
        unset($this->resources[$key]);
    }

    /**
     * {@inheritdoc}
     */
    public function has($key): bool
    {
        return isset($this->resources[$key]);
    }

    /**
     * {@inheritdoc}
     */
    public function clear(): void
    {
        $this->resources = [];
    }

    /**
     * {@inheritdoc}
     */
    public function keys(): array
    {
        return array_keys($this->resources);
    }

    /**
     * {@inheritdoc}
     */
    public function replace($resources): void
    {
        Assert::allIsInstanceOf($resources, 'Puli\Repository\Api\Resource\PuliResource');

        $this->resources = is_array($resources) ? $resources : iterator_to_array($resources);
    }

    /**
     * {@inheritdoc}
     */
    public function merge($resources): void
    {
        Assert::allIsInstanceOf($resources, PuliResource::class);

        // only start merging after validating all resources
        foreach ($resources as $resource) {
            $this->resources[] = $resource;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function isEmpty(): bool
    {
        return 0 === count($this->resources);
    }

    /**
     * {@inheritdoc}
     */
    public function offsetExists($key): bool
    {
        return $this->has($key);
    }

    /**
     * {@inheritdoc}
     */
    public function offsetGet($key): mixed
    {
        return $this->get($key);
    }

    /**
     * {@inheritdoc}
     */
    public function offsetSet($key, $value): void
    {
        if (null !== $key) {
            $this->set($key, $value);
        } else {
            $this->add($value);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function offsetUnset($key): void
    {
        $this->remove($key);
    }

    /**
     * {@inheritdoc}
     */
    public function getPaths()
    {
        return array_map(
            function (PuliResource $resource) {
                return $resource->getPath();
            },
            $this->resources
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getNames()
    {
        return array_map(
            function (PuliResource $resource) {
                return $resource->getName();
            },
            $this->resources
        );
    }

    public function count(): int
    {
        return count($this->resources);
    }

    public function getIterator($mode = ResourceCollectionIterator::KEY_AS_CURSOR): ResourceCollectionIterator
    {
        return new ResourceCollectionIterator($this, $mode);
    }

    public function toArray(): \Traversable|array
    {
        return $this->resources;
    }
}
