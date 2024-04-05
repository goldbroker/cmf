<?php

namespace Tests\Symfony\Cmf\Bundle\SonataPhpcrAdminIntegrationBundle\Unit\DependencyInjection;

use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractExtensionTestCase;
use Symfony\Cmf\Bundle\SonataPhpcrAdminIntegrationBundle\DependencyInjection\CmfSonataPhpcrAdminIntegrationExtension;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;

class CmfSonataAdminExtensionTest extends AbstractExtensionTestCase
{
    /**
     * {@inheritdoc}
     */
    protected function getContainerExtensions(): array
    {
        return [
            new CmfSonataPhpcrAdminIntegrationExtension(),
        ];
    }

    public function testThatBundlesAreConfigured()
    {
        $this->expectException(InvalidConfigurationException::class);
        $this->container->setParameter(
            'kernel.bundles',
            [
                'CmfSeoBundle' => true,
                'CmfRoutingBundle' => true,
                'SonataDoctrinePHPCRAdminBundle' => true,
                'DoctrinePHPCRBundle' => true,
                'BurgovKeyValueFormBundle' => true,
            ]
        );

        $this->load([]);
    }

    public function testEnhancerExists()
    {
        $this->container->setParameter(
            'kernel.bundles',
            [
                'SonataDoctrinePHPCRAdminBundle' => true,
                'SonataAdminBundle' => true,
            ]
        );

        $this->load(['bundles' => []]);

        $this->assertContainerBuilderHasServiceDefinitionWithTag(
            'cmf_sonata_phpcr_admin_integration.description.enhancer',
            'cmf_resource.description.enhancer',
            ['alias' => 'sonata_phpcr_admin']
        );
    }
}
