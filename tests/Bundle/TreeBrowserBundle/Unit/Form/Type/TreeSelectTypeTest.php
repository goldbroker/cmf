<?php

/*
 * This file is part of the Symfony CMF package.
 *
 * (c) 2011-2017 Symfony CMF
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tests\Symfony\Cmf\Bundle\TreeBrowserBundle\Unit\Form\Type;

use Symfony\Cmf\Bundle\TreeBrowserBundle\Form\Type\TreeSelectType;
use Symfony\Component\Form\Test\TypeTestCase;
use Symfony\Component\OptionsResolver\Exception\InvalidOptionsException;

class TreeSelectTypeTest extends TypeTestCase
{
    public function testSubmitText()
    {
        $form = $this->factory->create(TreeSelectType::class);

        $form->submit('/cms/content/about');

        $this->assertEquals('/cms/content/about', $form->getData());
    }

    public function testInvalidWidgetValue()
    {
        $this->expectException(InvalidOptionsException::class);

        $this->factory->create(TreeSelectType::class, null, [
            'widget' => 'fake',
        ]);
    }

    public function testBrowserWidget()
    {
        $form = $this->factory->create(TreeSelectType::class, null, [
            'widget' => 'browser',
        ]);

        $view = $form->createView();

        $this->assertEquals('hidden', $view->vars['type']);
    }

    public function testCompactWidget()
    {
        $form = $this->factory->create(TreeSelectType::class, null, [
            'widget' => 'compact',
        ]);

        $view = $form->createView();

        $this->assertEquals('text', $view->vars['type']);
    }
}
