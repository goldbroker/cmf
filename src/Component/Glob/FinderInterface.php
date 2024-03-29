<?php

namespace Symfony\Cmf\Component\Glob;

use Symfony\Cmf\Component\Resource\Puli\Api\ResourceCollection;

/**
 * Interface for glob finders
 */
interface FinderInterface
{
    /**
     * Locate a collection of resources from the
     * given locator.
     *
     * @param string $selector
     * @return ResourceCollection[]
     */
    public function find(string $selector): array;
}
