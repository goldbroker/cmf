<?php

declare(strict_types=1);

/*
 * This file is part of the Sonata Project package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\DoctrinePHPCRAdminBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

/**
 * Adds javascripts and stylesheets required for the TreeSelectType.
 *
 * @author Wouter de Jong <wouter@wouterj.nl>
 */
final class AddTreeBrowserAssetsPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition('sonata.admin.pool')) {
            return;
        }

        $this->addAssetsToAdminConfiguration($container->findDefinition('sonata.admin.configuration'));
        $this->addFormResources($container);
    }

    private function addAssetsToAdminConfiguration(Definition $definition)
    {
        $options = $definition->getArgument(2);
        $options['javascripts'][] = 'bundles/cmftreebrowser/js/cmf_tree_browser.fancytree.js';
        $options['stylesheets'][] = 'bundles/cmftreebrowser/css/cmf_tree_browser.fancytree.css';

        $definition->replaceArgument(2, $options);
    }

    private function addFormResources(ContainerBuilder $container)
    {
        $resources = $container->getParameter('twig.form.resources');
        $resources[] = '@SonataDoctrinePHPCRAdmin/Form/tree_browser_fields.html.twig';

        $container->setParameter('twig.form.resources', $resources);
    }
}
