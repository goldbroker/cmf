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

namespace Tests\Sonata\DoctrinePHPCRAdminBundle\Fixtures\App;

use Symfony\Cmf\Component\Testing\HttpKernel\TestKernel;
use Symfony\Component\Config\Loader\LoaderInterface;

class Kernel extends TestKernel
{
    public function __construct()
    {
        parent::__construct('test', true);
    }

    public function configure()
    {
        $this->registerConfiguredBundles();
        $this->requireBundleSet('default');
        $this->requireBundleSets(['phpcr_odm']);
    }

    public function getKernelDir()
    {
        try {
            $refl = new \ReflectionClass($this);
            $fname = $refl->getFileName();
            $kernelDir = \dirname($fname);

            return $kernelDir;
        } catch (\Exception $exception) {
        }
    }

    public function getCacheDir(): string
    {
        return sys_get_temp_dir().'/SonataDoctrinePhpcrAdminBundle/cache';
    }

    public function getLogDir(): string
    {
        return sys_get_temp_dir().'/SonataDoctrinePhpcrAdminBundle/logs';
    }

    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        $loader->load(__DIR__.'/config/config.php');
        $loader->load(__DIR__.'/config/admin-test.xml');
    }
}
