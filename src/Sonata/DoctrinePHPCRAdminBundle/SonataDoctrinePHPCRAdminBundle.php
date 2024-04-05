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

namespace Sonata\DoctrinePHPCRAdminBundle;

use Sonata\DoctrinePHPCRAdminBundle\DependencyInjection\Compiler\AddGuesserCompilerPass;
use Sonata\DoctrinePHPCRAdminBundle\DependencyInjection\Compiler\AddTemplatesCompilerPass;
use Sonata\DoctrinePHPCRAdminBundle\DependencyInjection\Compiler\AddTreeBrowserAssetsPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class SonataDoctrinePHPCRAdminBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        $container->addCompilerPass(new AddGuesserCompilerPass());
        $container->addCompilerPass(new AddTemplatesCompilerPass());
        $container->addCompilerPass(new AddTreeBrowserAssetsPass());
    }
}
