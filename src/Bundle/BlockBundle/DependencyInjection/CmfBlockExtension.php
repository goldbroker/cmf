<?php

/*
 * This file is part of the Symfony CMF package.
 *
 * (c) 2011-2017 Symfony CMF
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Cmf\Bundle\BlockBundle\DependencyInjection;

use Symfony\Cmf\Bundle\BlockBundle\DependencyInjection\Configuration;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

final class CmfBlockExtension extends Extension implements PrependExtensionInterface
{
    /**
     * {@inheritdoc}
     */
    public function prepend(ContainerBuilder $container)
    {
        // get all Bundles
        $bundles = $container->getParameter('kernel.bundles');

        if (isset($bundles['SonataBlockBundle'])) {
            $config = [
                'templates' => [
                    'block_base' => '@CmfBlock/Block/block_base.html.twig',
                ],
                'blocks_by_class' => [
                    0 => [
                        'class' => 'Symfony\\Cmf\\Bundle\\BlockBundle\\BlockBundle\\Doctrine\\Phpcr\\RssBlock',
                        'settings' => [
                            'title' => 'Insert the rss title',
                            'url' => false,
                            'maxItems' => 10,
                            'template' => '@CmfBlock/Block/block_rss.html.twig',
                            'itemClass' => 'Symfony\\Cmf\\Bundle\\BlockBundle\\BlockBundle\\Model\\FeedItem',
                        ],
                    ],
                ],
            ];
            $container->prependExtensionConfig('sonata_block', $config);
        }
    }

    public function load(array $configs, ContainerBuilder $container)
    {
        $config = $this->processConfiguration(new Configuration(), $configs);

        $container->setParameter($this->getAlias().'.twig.cmf_embed_blocks.prefix', $config['twig']['cmf_embed_blocks']['prefix']);
        $container->setParameter($this->getAlias().'.twig.cmf_embed_blocks.postfix', $config['twig']['cmf_embed_blocks']['postfix']);

        // detect bundles
        $bundles = $container->getParameter('kernel.bundles');

        // load config
        $loader = new XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.xml');

        if ($config['persistence']['phpcr']['enabled']) {
            $this->loadPhpcr($config['persistence']['phpcr'], $loader, $container);
        }
    }

    private function loadPhpcr(array $config, XmlFileLoader $loader, ContainerBuilder $container)
    {
        $container->setParameter($this->getAlias().'.backend_type_phpcr', true);

        $keys = [
            'block_basepath',
            'manager_name',
        ];

        foreach ($keys as $key) {
            $container->setParameter($this->getAlias().'.persistence.phpcr.'.$key, $config[$key]);
        }

        $loader->load('persistence-phpcr.xml');

        $blockLoader = $container->getDefinition('cmf.block.service');
        $blockLoader->replaceArgument(0, new Reference('doctrine_phpcr'));
        $blockLoader->addMethodCall('setManagerName', ['%cmf_block.persistence.phpcr.manager_name%']);

        $bundles = $container->getParameter('kernel.bundles');
        if (isset($bundles['CmfCreateBundle'])) {
            $blockLoader = $container->getDefinition('cmf.block.simple');
            $blockLoader->addMethodCall('setTemplate', ['@CmfBlock/Block/block_simple_createphp.html.twig']);
            $blockLoader = $container->getDefinition('cmf.block.string');
            $blockLoader->addMethodCall('setTemplate', ['@CmfBlock/Block/block_string_createphp.html.twig']);
        }

        if (isset($bundles['CmfMenuBundle'])) {
            $loader->load('menu.xml');
        }
    }

    /**
     * Returns the base path for the XSD files.
     *
     * @return string The XSD base path
     */
    public function getXsdValidationBasePath()
    {
        return __DIR__.'/../Resources/config/schema';
    }

    public function getNamespace()
    {
        return 'http://cmf.symfony.com/schema/dic/block';
    }
}
