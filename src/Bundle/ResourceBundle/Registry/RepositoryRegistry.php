<?php

/*
 * This file is part of the Symfony CMF package.
 *
 * (c) 2011-2017 Symfony CMF
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Cmf\Bundle\ResourceBundle\Registry;

use Symfony\Cmf\Component\Resource\Puli\Api\ResourceRepository;
use Symfony\Cmf\Component\Resource\RepositoryRegistryInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Repository registry which uses pre-registered serviceMap to create
 * the repository instances according to the configuration.
 *
 * @author Daniel Leech <daniel@dantleech.com>
 */
class RepositoryRegistry implements RepositoryRegistryInterface
{
    private $container;

    private $serviceMap = [];

    private $typeMap = [];

    private $names = [];

    private $defaultRepositoryName;

    /**
     * @param ContainerInterface $container
     * @param array              $serviceMap
     * @param array              $typeMap
     * @param string             $defaultRepositoryName
     */
    public function __construct(
        ContainerInterface $container,
        array $serviceMap,
        array $typeMap,
        $defaultRepositoryName
    ) {
        $this->container = $container;
        $this->serviceMap = $serviceMap;
        $this->typeMap = $typeMap;
        $this->defaultRepositoryName = $defaultRepositoryName;
    }

    /**
     * {@inheritdoc}
     */
    public function names(): array
    {
        return array_keys($this->serviceMap);
    }

    /**
     * {@inheritdoc}
     */
    public function all(): array
    {
        $repositories = [];
        foreach (array_keys($this->serviceMap) as $name) {
            $repositories[$name] = $this->get($name);
        }

        return $repositories;
    }

    /**
     * {@inheritdoc}
     */
    public function get(?string $name = null): ResourceRepository
    {
        if (null === $name) {
            $repositoryName = $this->defaultRepositoryName;
        }

        if (!isset($this->serviceMap[$name])) {
            throw new \InvalidArgumentException(sprintf(
                'Repository "%s" has not been registered, available repositories: "%s".',
                $name,
                implode('", "', array_keys($this->serviceMap))
            ));
        }

        /** @var ResourceRepository $repository */
        $repository = $this->container->get($this->serviceMap[$name]);
        $this->names[spl_object_hash($repository)] = $name;

        return $repository;
    }

    /**
     * {@inheritdoc}
     */
    public function getRepositoryName(ResourceRepository $resource): string
    {
        $hash = spl_object_hash($resource);
        if (isset($this->names[$hash])) {
            return $this->names[$hash];
        }

        throw new \RuntimeException(sprintf(
            'Repository of class "%s" was not instantiated by this registry, I '.
            'don\'t know what its name is.',
            get_class($resource)
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getRepositoryType(ResourceRepository $resource): string
    {
        $class = get_class($resource);
        if (!isset($this->typeMap[$class])) {
            throw new \RuntimeException(sprintf(
                'Repository of class "%s" is not known, could not determine its type.',
                $class
            ));
        }

        return $this->typeMap[$class];
    }
}
