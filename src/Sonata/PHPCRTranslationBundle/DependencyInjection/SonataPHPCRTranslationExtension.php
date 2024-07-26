<?php

declare(strict_types=1);

namespace Sonata\PHPCRTranslationBundle\DependencyInjection;

use Sonata\PHPCRTranslationBundle\Model\TranslatableInterface as PHPCRTranslatableInterface;
use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;
use Symfony\Component\DependencyInjection\Loader\PhpFileLoader;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Cmf\Bundle\CoreBundle\Translatable\TranslatableInterface;

class SonataPHPCRTranslationExtension extends Extension implements ExtensionInterface, CompilerPassInterface
{
    private array $translationTargets = [];

    public function load(array $configs, ContainerBuilder $container)
    {
        $processor = new Processor();
        $configuration = new Configuration();
        $config = $processor->processConfiguration($configuration, $configs);

        $this->translationTargets = [];

        if ($config['enabled']) {
            $loader = new XmlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
            $loader->load('service_phpcr.xml');

            /**
             * @phpstan-var list<class-string>
             */
            $listOfInterfaces = array_merge(
                [
                    PHPCRTranslatableInterface::class,
                    TranslatableInterface::class,
                ],
                $config['implements']
            );
            $this->translationTargets['phpcr']['implements'] = $listOfInterfaces;

            /**
             * @phpstan-var list<class-string>
             */
            $listOfClasses = $config['instanceof'];
            $this->translationTargets['phpcr']['instanceof'] = $listOfClasses;

            // Load initial sonata-translation services (in case it is not enabled - the services won't be loaded)
            $sonataTranslationDir = $container->getParameter('kernel.project_dir') . '/vendor/sonata-project/translation-bundle/';
            $sonataTranslationLoader = new PhpFileLoader($container, new FileLocator($sonataTranslationDir . 'src/Resources/config'));
            $sonataTranslationLoader->load('block.php');
            $sonataTranslationLoader->load('listener.php');
            $sonataTranslationLoader->load('twig.php');
        }

        $container->setParameter('sonata_phpcr_translation.targets', $this->translationTargets);
    }

    public function process(ContainerBuilder $container)
    {
        $this->configureChecker($container, $this->translationTargets);
    }

    /**
     * @param array $translationTargets
     *
     * @phpstan-param array{
     *  phpcr?: array{implements: list<class-string>, instanceof: list<class-string>}
     * } $translationTargets
     *
     * @return void
     */
    protected function configureChecker(ContainerBuilder $container, $translationTargets)
    {
        if (!$container->hasDefinition('sonata_translation.checker.translatable')) {
            return;
        }

        $initialTranslationTargets = $container->getParameter('sonata_translation.targets') ?: [];

        $translatableCheckerDefinition = $container->getDefinition('sonata_translation.checker.translatable');

        $supportedInterfaces = [];
        $supportedModels = [];
        foreach (array_merge($translationTargets, $initialTranslationTargets) as $targets) {
            $supportedInterfaces = array_merge($supportedInterfaces, $targets['implements']);
            $supportedModels = array_merge($supportedModels, $targets['instanceof']);
        }

        $translatableCheckerDefinition->addMethodCall('setSupportedInterfaces', [$supportedInterfaces]);
        $translatableCheckerDefinition->addMethodCall('setSupportedModels', [$supportedModels]);
    }

}
