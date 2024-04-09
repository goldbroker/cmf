<?php

/*
 * This file is part of the Symfony CMF package.
 *
 * (c) Symfony CMF
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tests\Symfony\Cmf\Bundle\CoreBundle\Fixtures\App;

use Symfony\Cmf\Bundle\CoreBundle\CmfCoreBundle;
use Symfony\Cmf\Bundle\RoutingBundle\CmfRoutingBundle;
use Symfony\Cmf\Component\Testing\HttpKernel\TestKernel;
use Symfony\Component\Config\Loader\LoaderInterface;

class Kernel extends TestKernel
{
    public function configure()
    {
        $this->requireBundleSets(['default', 'phpcr_odm']);

        $this->addBundles([
            new CmfRoutingBundle(),
            new CmfCoreBundle(),
        ]);
    }

    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        $loader->load(__DIR__.'/config/config.php');
    }
}
