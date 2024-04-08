<?php

/*
 * This file is part of the Symfony CMF package.
 *
 * (c) Symfony CMF
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tests\Symfony\Cmf\Bundle\CoreBundle\Unit\Form;

use Symfony\Cmf\Bundle\CoreBundle\Form\Type\CheckboxUrlLabelFormType;
use Symfony\Component\Form\PreloadedExtension;
use Symfony\Component\Form\Test\TypeTestCase;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\RouterInterface;

class Router implements RouterInterface
{
    public function setContext(RequestContext $context)
    {
    }

    public function getContext()
    {
    }

    public function match($pathinfo)
    {
    }

    public function getRouteCollection()
    {
    }

    public function generate($name, $parameters = [], $absolute = false): string
    {
        return '/test/'.$name;
    }
}

class CheckboxUrlLabelFormTypeTest extends TypeTestCase
{
    public function testContentPathsAreSet()
    {
        $checkboxUrlLabelForm = $this->factory->create(CheckboxUrlLabelFormType::class, null, [
            'routes' => ['a' => ['name' => 'a'], 'b' => ['name' => 'b']],
        ]);
        $view = $checkboxUrlLabelForm->createView();

        $this->assertSame('/test/a', $view->vars['paths']['a']);
        $this->assertSame('/test/b', $view->vars['paths']['b']);
    }

    protected function getExtensions(): array
    {
        return array_merge(parent::getExtensions(), [
            new PreloadedExtension([
                CheckboxUrlLabelFormType::class => new CheckboxUrlLabelFormType(new Router()),
            ], []),
        ]);
    }
}
