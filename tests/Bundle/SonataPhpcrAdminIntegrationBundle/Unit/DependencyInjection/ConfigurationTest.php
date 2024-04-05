<?php

/*
 * This file is part of the Symfony CMF package.
 *
 * (c) 2011-2017 Symfony CMF
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tests\Symfony\Cmf\Bundle\SonataPhpcrAdminIntegrationBundle\Unit\DependencyInjection;

use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractExtensionConfigurationTestCase;
use Symfony\Cmf\Bundle\SonataPhpcrAdminIntegrationBundle\DependencyInjection\CmfSonataPhpcrAdminIntegrationExtension;
use Symfony\Cmf\Bundle\SonataPhpcrAdminIntegrationBundle\DependencyInjection\Configuration;
use Symfony\Cmf\Bundle\SonataPhpcrAdminIntegrationBundle\DependencyInjection\Factory;
use Symfony\Component\Config\Definition\ConfigurationInterface;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;

/**
 * This test will try to cover all configs.
 *
 * Means check if all available formats are equal.
 *
 * @author Maximilian Berghoff <Maximilian.Berghoff@gmx.de>
 */
class ConfigurationTest extends AbstractExtensionConfigurationTestCase
{
    protected function getContainerExtension(): ExtensionInterface
    {
        return new CmfSonataPhpcrAdminIntegrationExtension();
    }

    protected function getConfiguration(): ConfigurationInterface
    {
        return new Configuration([
            'seo' => new Factory\SeoAdminFactory(),
            'core' => new Factory\CoreAdminFactory(),
        ]);
    }

    public function testDefaultsForAllConfigFormats()
    {
        $expectedConfiguration = [
            'bundles' => [
                'seo' => [
                    'enabled' => true,
                    'extensions' => [
                        'metadata' => [
                            'form_group' => 'form.group_seo',
                            'form_tab' => 'form.tab_seo',
                        ],
                    ],
                ],
                'core' => [
                    'enabled' => true,
                    'extensions' => [
                        'publishable' => [
                            'form_group' => 'form.group_publish_workflow',
                            'form_tab' => 'form.tab_publish',
                        ],
                        'publish_time' => [
                            'form_group' => 'form.group_publish_workflow',
                            'form_tab' => 'form.tab_publish',
                        ],
                    ],
                ],
            ],
        ];

        $sources = array_map(function ($path) {
            return __DIR__.'/../../Fixtures/fixtures/'.$path;
        }, [
            'config/config.yml',
            'config/config.php',
            'config/config.xml',
        ]);

        $this->assertProcessedConfigurationEquals($expectedConfiguration, $sources);
    }
}
