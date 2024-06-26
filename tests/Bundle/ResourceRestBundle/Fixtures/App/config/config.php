<?php

/*
 * This file is part of the Symfony CMF package.
 *
 * (c) 2011-2017 Symfony CMF
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Tests\Symfony\Cmf\Bundle\ResourceRestBundle\Fixtures\App\Description\DummyEnhancer;

$container->setParameter('cmf_testing.bundle_fqn', 'Symfony\Cmf\Bundle\ResourceRestBundle');
$loader->import(CMF_TEST_CONFIG_DIR.'/dist/parameters_sf5.yml');
$loader->import(CMF_TEST_CONFIG_DIR.'/dist/framework.php');
$loader->import(CMF_TEST_CONFIG_DIR.'/dist/monolog.yml');
$loader->import(CMF_TEST_CONFIG_DIR.'/dist/doctrine.yml');
$loader->import(CMF_TEST_CONFIG_DIR.'/dist/security.php');
$loader->import(CMF_TEST_CONFIG_DIR.'/phpcr_odm.php');

$container->register('app.dummy_enhancer', DummyEnhancer::class)
    ->addTag('cmf_resource.description.enhancer', ['alias' => 'dummy']);
