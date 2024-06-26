<?php

/*
 * This file is part of the Symfony CMF package.
 *
 * (c) 2011-2017 Symfony CMF
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tests\Symfony\Cmf\Bundle\SeoBundle\Unit\DependencyInjection\Compiler;

use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractCompilerPassTestCase;
use Symfony\Cmf\Bundle\SeoBundle\DependencyInjection\Compiler\RegisterExtractorsPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

class RegisterExtractorsPassTest extends AbstractCompilerPassTestCase
{
    protected function registerCompilerPass(ContainerBuilder $container): void
    {
        $container->addCompilerPass(new RegisterExtractorsPass());
    }

    public function testRegistersServicesWithExtractorTagAndDefaultPriority()
    {
        $nonExtractorService = new Definition();
        $this->setDefinition('some_service', $nonExtractorService);

        $extractorService = new Definition();
        $extractorService->addTag('cmf_seo.extractor');
        $this->setDefinition('extractor_service', $extractorService);

        $presentationService = new Definition();
        $this->setDefinition('cmf_seo.presentation', $presentationService);

        $this->compile();

        $this->assertContainerBuilderHasServiceDefinitionWithMethodCall(
            'cmf_seo.presentation',
            'addExtractor',
            [new Reference('extractor_service'), 0]
        );
    }

    public function testRegistersServicesWithExtractorTagAndPriority()
    {
        $nonExtractorService = new Definition();
        $this->setDefinition('some_service', $nonExtractorService);

        $extractorService = new Definition();
        $extractorService->addTag('cmf_seo.extractor', ['priority' => 1]);
        $this->setDefinition('extractor_service', $extractorService);

        $presentationService = new Definition();
        $this->setDefinition('cmf_seo.presentation', $presentationService);

        $this->compile();

        $this->assertContainerBuilderHasServiceDefinitionWithMethodCall(
            'cmf_seo.presentation',
            'addExtractor',
            [new Reference('extractor_service'), 1]
        );
    }
}
