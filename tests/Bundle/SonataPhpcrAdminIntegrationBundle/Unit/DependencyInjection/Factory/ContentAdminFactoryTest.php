<?php

/*
 * This file is part of the Symfony CMF package.
 *
 * (c) 2011-2017 Symfony CMF
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tests\Symfony\Cmf\Bundle\SonataPhpcrAdminIntegrationBundle\Unit\DependencyInjection\Factory;

use Symfony\Cmf\Bundle\SonataPhpcrAdminIntegrationBundle\DependencyInjection\Factory\ContentAdminFactory;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;
use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;

/**
 * @author Wouter de Jong <wouter@wouterj.nl>
 * @author Maximilian Berghoff <Maximilian.Berghoff@mayflower.de>
 */
class ContentAdminFactoryTest extends \PHPUnit\Framework\TestCase
{
    private $factory;

    private $container;

    private $fileLoader;

    protected function setUp(): void
    {
        $this->factory = new ContentAdminFactory();
        $this->container = new ContainerBuilder();
        $this->container->setParameter('kernel.bundles', ['FOSCKEditorBundle' => true]);
        $this->fileLoader = $this->createMock(XmlFileLoader::class);
    }

    public function testInvalidCKEditorEnabledWithoutConfigName()
    {
        $this->expectException(InvalidConfigurationException::class);
        $this->expectExceptionMessage('config_name setting has to be defined when FOSCKEditorBundle integration is enabled');

        $config = $this->process($this->buildConfig(), [[
            'bundles' => [
                'content' => true,
            ],
        ]]);

        $this->create($config);
    }

    public function testCKEditorDisabledWithoutConfigName()
    {
        $config = $this->process($this->buildConfig(), [[
            'bundles' => [
                'content' => ['fos_ckeditor' => false],
            ],
        ]]);

        $this->create($config);

        $this->assertEquals([], $this->container->getParameter('cmf_sonata_phpcr_admin_integration.content.fos_ckeditor'));
    }

    public function testCKEditorEnabledWithConfigName()
    {
        $config = $this->process($this->buildConfig(), [[
            'bundles' => [
                'content' => ['fos_ckeditor' => ['config_name' => 'default']],
            ],
        ]]);

        $this->create($config);

        $this->assertEquals(['config_name' => 'default'], $this->container->getParameter('cmf_sonata_phpcr_admin_integration.content.fos_ckeditor'));
    }

    protected function buildConfig(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('cmf_sonata_phpcr_admin_integration');

        $bundles = $treeBuilder->getRootNode()->children()->arrayNode('bundles')->isRequired()->children();
        $config = $bundles->arrayNode($this->factory->getKey())
            ->addDefaultsIfNotSet()
            ->canBeEnabled()
            ->children();

        $this->factory->addConfiguration($config);

        return $treeBuilder;
    }

    protected function process(TreeBuilder $treeBuilder, array $configs): array
    {
        $processor = new Processor();

        return $processor->process($treeBuilder->buildTree(), $configs);
    }

    protected function create(array $processedConfig)
    {
        $this->factory->create($processedConfig['bundles'][$this->factory->getKey()], $this->container, $this->fileLoader);
    }
}
