<?php

/*
 * This file is part of the Symfony CMF package.
 *
 * (c) 2011-2017 Symfony CMF
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tests\Symfony\Cmf\Bundle\ResourceRestBundle\Unit\Serializer\Jms\Handler;

use JMS\Serializer\Context;
use JMS\Serializer\GraphNavigatorInterface;
use JMS\Serializer\Visitor\SerializationVisitorInterface;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Symfony\Cmf\Bundle\ResourceRestBundle\Registry\PayloadAliasRegistry;
use Symfony\Cmf\Bundle\ResourceRestBundle\Serializer\Jms\Handler\ResourceHandler;
use Symfony\Cmf\Component\Resource\Description\Description;
use Symfony\Cmf\Component\Resource\Description\DescriptionFactory;
use Symfony\Cmf\Component\Resource\Puli\Api\ResourceRepository;
use Symfony\Cmf\Component\Resource\Repository\Resource\CmfResource;
use Symfony\Cmf\Component\Resource\RepositoryRegistryInterface;

class ResourceHandlerTest extends TestCase
{
    private $repositoryRegistry;

    private $payloadAliasRegistry;

    private $descriptionFactory;

    private $visitor;

    private $resource;

    private $childResource;

    private $repository;

    private $context;

    private $handler;

    private $description;

    private $navigator;

    protected function setUp(): void
    {
        $this->repositoryRegistry = $this->prophesize(RepositoryRegistryInterface::class);
        $this->payloadAliasRegistry = $this->prophesize(PayloadAliasRegistry::class);
        $this->visitor = $this->prophesize(SerializationVisitorInterface::class);
        $this->resource = $this->prophesize(CmfResource::class);
        $this->childResource = $this->prophesize(CmfResource::class);

        $this->repository = $this->prophesize(ResourceRepository::class);
        $this->context = $this->prophesize(Context::class);
        $this->navigator = $this->prophesize(GraphNavigatorInterface::class);

        $this->description = $this->prophesize(Description::class);
        $this->description->all()->willReturn([]);
        $this->descriptionFactory = $this->prophesize(DescriptionFactory::class);
        $this->descriptionFactory->getPayloadDescriptionFor(Argument::any())->willReturn($this->description->reveal());

        $this->handler = new ResourceHandler(
            $this->repositoryRegistry->reveal(),
            $this->payloadAliasRegistry->reveal(),
            $this->descriptionFactory->reveal()
        );

        $this->resource->getRepository()->willReturn($this->repository);
    }

    public function testHandler()
    {
        $this->repositoryRegistry->getRepositoryName($this->repository)->willReturn('repo');
        $this->repositoryRegistry->getRepositoryType($this->repository)->willReturn('repo_type');
        $this->payloadAliasRegistry->getPayloadAlias($this->resource->reveal())->willReturn('alias');
        $this->resource->getPayloadType()->willReturn('payload_type');
        $this->resource->getPayload()->willReturn(null);
        $this->resource->getPath()->willReturn('/path/to');
        $this->resource->getRepositoryPath()->willReturn('/repository/path');
        $this->resource->listChildren()->willReturn([
            $this->childResource,
        ]);

        $this->payloadAliasRegistry->getPayloadAlias($this->childResource->reveal())->willReturn('alias');
        $this->childResource->getPayloadType()->willReturn('payload_type');
        $this->childResource->getPayload()->willReturn(null);
        $this->childResource->getPath()->willReturn('/path/to/child');
        $this->childResource->getRepositoryPath()->willReturn('/child/repository/path');
        $this->childResource->getRepository()->willReturn($this->repository->reveal());
        $this->childResource->listChildren()->willReturn([]);

        $this->expectNotToPerformAssertions();

        $expected = [
            'repository_alias' => 'repo',
            'repository_type' => 'repo_type',
            'payload_alias' => 'alias',
            'payload_type' => 'payload_type',
            'path' => '/path/to',
            'node_name' => 'to',
            'label' => 'to',
            'repository_path' => '/repository/path',
            'children' => [
                [
                    'repository_alias' => 'repo',
                    'repository_type' => 'repo_type',
                    'payload_alias' => 'alias',
                    'payload_type' => 'payload_type',
                    'path' => '/path/to/child',
                    'label' => 'child',
                    'node_name' => 'child',
                    'repository_path' => '/child/repository/path',
                    'children' => [],
                    'descriptors' => [],
                ],
            ],
            'descriptors' => [],
        ];

        $this->context->getNavigator()->willReturn($this->navigator);
        $this->navigator->accept($expected)->willReturn($this->context);

        $this->handler->serializeResource(
            $this->visitor->reveal(),
            $this->resource->reveal(),
            [],
            $this->context->reveal()
        );
    }
}
