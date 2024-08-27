<?php

declare(strict_types=1);

namespace Sonata\PHPCRTranslationBundle;

use Sonata\PHPCRTranslationBundle\DependencyInjection\Compiler\AdminExtensionCompilerPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class SonataPHPCRTranslationBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(new AdminExtensionCompilerPass());
    }
}
