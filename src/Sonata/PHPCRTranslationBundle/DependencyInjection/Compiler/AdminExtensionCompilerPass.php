<?php

declare(strict_types=1);

namespace Sonata\PHPCRTranslationBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class AdminExtensionCompilerPass implements CompilerPassInterface
{
    /**
     * @return void
     */
    public function process(ContainerBuilder $container)
    {
        $translationTargets = $container->getParameter('sonata_phpcr_translation.targets');
        \assert(\is_array($translationTargets));

        $reference = new Reference('sonata_translation.admin.extension.phpcr_translatable');

        foreach ($container->findTaggedServiceIds('sonata.admin') as $id => $attributes) {
            $admin = $container->getDefinition($id);

            $tagData = $admin->getTag('sonata.admin')[0];
            $modelClass = $tagData['model_class'] ?? $admin->getArgument(1);
            $modelClass = $container->getParameterBag()->resolveValue($modelClass);

            if (!$modelClass || !class_exists($modelClass)) {
                continue;
            }
            $modelClassReflection = new \ReflectionClass($modelClass);

            foreach ($translationTargets['phpcr']['implements'] as $interface) {
                if ($modelClassReflection->implementsInterface($interface)) {
                    $admin->addMethodCall('addExtension', [$reference]);
                }
            }
            foreach ($translationTargets['phpcr']['instanceof'] as $class) {
                if ($modelClassReflection->getName() === $class || $modelClassReflection->isSubclassOf($class)) {
                    $admin->addMethodCall('addExtension', [$reference]);
                }
            }
        }
    }
}
