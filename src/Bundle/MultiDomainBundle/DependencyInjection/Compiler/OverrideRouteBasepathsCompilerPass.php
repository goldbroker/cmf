<?php

namespace Symfony\Cmf\Bundle\MultiDomainBundle\DependencyInjection\Compiler;

use Symfony\Cmf\Bundle\MultiDomainBundle\Doctrine\Phpcr\HostCandidates;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class OverrideRouteBasepathsCompilerPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasParameter('cmf_multi_domain.backend_type_phpcr')) {
            return;
        }

        $routeBasePaths = $container->getParameter('cmf_routing.dynamic.persistence.phpcr.route_basepaths');
        $domains = $container->getParameter('cmf_multi_domain.domains');

        $routeBasePathsWithLocale = array();

        foreach ($routeBasePaths as $routeBasePath) {
            foreach ($domains as $locale => $domain) {
                $routeBasePathsWithLocale[] = sprintf('%s/%s', $routeBasePath, $locale);
            }
        }

        $container
            ->getDefinition('cmf_routing.phpcr_candidates_prefix')
            ->setClass(HostCandidates::class)
            ->replaceArgument(0, $routeBasePathsWithLocale)
            ->replaceArgument(1, $domains)
            ->setArgument(4, $routeBasePaths)
        ;
    }
}
