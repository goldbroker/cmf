<?php

/*
 * This file is part of the Symfony CMF package.
 *
 * (c) 2011-2017 Symfony CMF
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Doctrine\Bundle\PHPCRBundle\DependencyInjection\Compiler\DoctrinePhpcrMappingsPass;

$loader->import(CMF_TEST_CONFIG_DIR.'/default.php');
$loader->import(__DIR__.'/security.yml');
$loader->import(__DIR__.'/cmf_seo.yml');

$phpcrCompilerClass = 'Doctrine\Bundle\PHPCRBundle\DependencyInjection\Compiler\DoctrinePhpcrMappingsPass';
if (class_exists($phpcrCompilerClass)) {
    $container->addCompilerPass(
        DoctrinePhpcrMappingsPass::createAnnotationMappingDriver(
            [
                'Tests\Symfony\Cmf\Bundle\SeoBundle\Fixtures\App\Document'
            ],
            [
                realpath(__DIR__ . '/../Document')
            ]
        ));
}