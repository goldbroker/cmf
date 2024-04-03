<?php

namespace Symfony\Cmf\Bundle\MultiDomainBundle;

use Symfony\Cmf\Bundle\MultiDomainBundle\DependencyInjection\Compiler\OverrideRouteBasepathsCompilerPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class CmfMultiDomainBundle extends Bundle
{
    /**
     * {@inheritDoc}
     */
    public function build(ContainerBuilder $container)
    {
        $container->addCompilerPass(new OverrideRouteBasepathsCompilerPass());
    }
}
