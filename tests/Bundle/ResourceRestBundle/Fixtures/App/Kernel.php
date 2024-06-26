<?php

/*
 * This file is part of the Symfony CMF package.
 *
 * (c) 2011-2017 Symfony CMF
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tests\Symfony\Cmf\Bundle\ResourceRestBundle\Fixtures\App;

use Tests\Symfony\Cmf\Bundle\ResourceRestBundle\Features\Context\ResourceContext;
use Symfony\Cmf\Component\Testing\HttpKernel\TestKernel;
use Symfony\Component\Config\Loader\LoaderInterface;

/**
 * This is the kernel used by the application being tested.
 */
class Kernel extends TestKernel
{
    public function configure()
    {
        $this->requireBundleSets([
            'default', 'phpcr_odm',
        ]);

        $this->registerConfiguredBundles();
    }

    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        $loader->load(__DIR__.'/config/config.php');

        if ('behat' !== $this->getEnvironment() && file_exists(ResourceContext::getConfigurationFile())) {
            $loader->import(ResourceContext::getConfigurationFile());
        }
    }
}
