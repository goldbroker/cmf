<?php

declare(strict_types=1);

namespace Sonata\PHPCRTranslationBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('sonata_phpcr_translation');

        $rootNode = $treeBuilder->getRootNode();

        $rootNode
            ->children()
                ->scalarNode('enabled')->end()
                ->arrayNode('implements')
                    ->requiresAtLeastOneElement()
                    ->prototype('scalar')->end()
                ->end()
                ->arrayNode('instanceof')
                    ->requiresAtLeastOneElement()
                    ->prototype('scalar')->end()
                ->end()
            ->end();

        return $treeBuilder;
    }
}
