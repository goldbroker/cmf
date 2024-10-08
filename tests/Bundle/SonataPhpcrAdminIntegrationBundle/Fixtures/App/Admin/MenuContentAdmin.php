<?php

/*
 * This file is part of the Symfony CMF package.
 *
 * (c) 2011-2017 Symfony CMF
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tests\Symfony\Cmf\Bundle\SonataPhpcrAdminIntegrationBundle\Fixtures\App\Admin;

use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\DoctrinePHPCRAdminBundle\Admin\Admin;

/**
 * Common base admin for Menu and MenuNode.
 */
class MenuContentAdmin extends Admin
{
    protected $baseRouteName = 'cmf_menu_test_content';

    protected $baseRoutePattern = '/cmf/menu-test/content';

    protected function configureListFields(ListMapper $list): void
    {
        $list
            ->addIdentifier('id', 'text')
            ->add('title', 'text')
        ;

        $list
            ->add('locales', 'choice', [
                'template' => 'SonataDoctrinePHPCRAdminBundle:CRUD:locales.html.twig',
            ])
        ;
    }

    protected function configureFormFields(FormMapper $form): void
    {
        $form
            ->with('form.group_general')
                ->add('title', 'text')
            ->end()
        ;
    }
}
