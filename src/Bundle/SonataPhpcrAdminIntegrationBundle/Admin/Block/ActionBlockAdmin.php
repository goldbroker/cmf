<?php

/*
 * This file is part of the Symfony CMF package.
 *
 * (c) 2011-2017 Symfony CMF
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Cmf\Bundle\SonataPhpcrAdminIntegrationBundle\Admin\Block;

use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\DoctrinePHPCRAdminBundle\Filter\NodeNameFilter;
use Symfony\Component\Form\Extension\Core\Type\TextType;

/**
 * @author Lukas Kahwe Smith <smith@pooteeweet.org>
 */
class ActionBlockAdmin extends AbstractBlockAdmin
{
    /**
     * {@inheritdoc}
     */
    protected function configureListFields(ListMapper $list): void
    {
        $list
            ->addIdentifier('id', 'text')
            ->add('name', 'text')
            ->add('actionName', 'text')
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function configureFormFields(FormMapper $form): void
    {
        parent::configureFormFields($form);

        $form
            ->tab('form.tab_general')
                ->with('form.group_block', ['class' => 'col-md-9'])
                    ->add('actionName', TextType::class)
                ->end()
            ->end()
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function configureDatagridFilters(DatagridMapper $filter): void
    {
        $filter
            ->add('name', NodeNameFilter::class)
        ;
    }
}
