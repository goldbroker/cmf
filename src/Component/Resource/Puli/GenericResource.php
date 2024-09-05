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
use Symfony\Cmf\Component\Resource\Puli\Api\ResourceMetadata;
use Symfony\Cmf\Component\Resource\Puli\Api\ResourceRepository;

/**
 * A generic resource.
 *
 * @since  1.0
 *
 * @author Bernhard Schussek <bschussek@gmail.com>
 */
class GenericResource implements PuliResource
{
    /**
     * @var ResourceRepository
     */
    private $repo;

    /**
     * @var string
     */
    private $path;

    /**
     * @var string
     */
    private $repoPath;

    /**
     * Creates a new resource.
     *
     * @param string|null $path the path of the resource
     */
    public function __construct($path = null)
    {
        $this->path = $path;
        $this->repoPath = $path;
    }

    /**
     * {@inheritdoc}
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return $this->path ? basename($this->path) : null;
    }

    /**
     * {@inheritdoc}
     */
    public function getChild($relPath)
    {
        if (!$this->getRepository()) {
            throw \RuntimeException(sprintf(
                'The resource %s does not exist.',
                $this->getRepositoryPath().'/'.$relPath
            ));
        }

        return $this->getRepository()->get($this->getRepositoryPath().'/'.$relPath);
    }

    /**
     * {@inheritdoc}
     */
    public function hasChild($relPath)
    {
        if (!$this->getRepository()) {
            return false;
        }

        return $this->getRepository()->contains($this->getRepositoryPath().'/'.$relPath);
    }

    /**
     * {@inheritdoc}
     */
    public function hasChildren()
    {
        if (!$this->getRepository()) {
            return false;
        }

        return $this->getRepository()->hasChildren($this->getRepositoryPath());
    }

    /**
     * {@inheritdoc}
     */
    public function listChildren()
    {
        $children = new ArrayResourceCollection();

        if (!$this->getRepository()) {
            return $children;
        }

        foreach ($this->getRepository()->listChildren($this->getRepositoryPath()) as $child) {
            $children[$child->getName()] = $child;
        }

        return $children;
    }

    /**
     * {@inheritdoc}
     */
    public function getMetadata()
    {
        return new ResourceMetadata();
    }

    /**
     * {@inheritdoc}
     */
    public function attachTo(ResourceRepository $repo, $path = null)
    {
        $this->repo = $repo;

        if (null !== $path) {
            $this->path = $path;
            $this->repoPath = $path;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function detach()
    {
        $this->repo = null;
    }

    /**
     * {@inheritdoc}
     */
    public function getRepository()
    {
        return $this->repo;
    }

    /**
     * {@inheritdoc}
     */
    public function getRepositoryPath()
    {
        return $this->repoPath;
    }

    /**
     * {@inheritdoc}
     */
    public function isAttached()
    {
        return null !== $this->repo;
    }

    /**
     * {@inheritdoc}
     */
    public function createReference($path)
    {
        $ref = clone $this;
        $ref->path = $path;

        return $ref;
    }

    /**
     * {@inheritdoc}
     */
    public function isReference()
    {
        return $this->path !== $this->repoPath;
    }

    public function __serialize(): array
    {
        return [
            'path' => $this->path,
            'repoPath' => $this->repoPath,
        ];
    }

    public function __unserialize(array $serialized): void
    {
        $this->path = $serialized['path'];
        $this->repoPath = $serialized['repoPath'];
    }
}
