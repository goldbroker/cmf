<?php

/*
 * This file is part of the Symfony CMF package.
 *
 * (c) 2011-2017 Symfony CMF
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Cmf\Bundle\SonataPhpcrAdminIntegrationBundle\Admin\Routing;

use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\DoctrinePHPCRAdminBundle\Filter\NodeNameFilter;
use Symfony\Cmf\Bundle\RoutingBundle\Model\Route;
use Symfony\Cmf\Bundle\SonataPhpcrAdminIntegrationBundle\Admin\AbstractAdmin;
use Symfony\Cmf\Bundle\TreeBrowserBundle\Form\Type\TreeSelectType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class RedirectRouteAdmin extends AbstractAdmin
{
    protected function configureListFields(ListMapper $list): void
    {
        $list->addIdentifier('path', 'text');
    }

    protected function configureFormFields(FormMapper $form): void
    {
        $form
            ->with('form.group_location', ['class' => 'col-md-3'])
                ->add(
                    'parentDocument',
                    TreeSelectType::class,
                    ['root_node' => $this->getRootPath(), 'widget' => 'browser']
                )
                ->add('name', TextType::class)
            ->end()

            ->with('form.group_target', ['class' => 'col-md-9'])
                ->add('routeName', TextType::class, ['required' => false])
                ->add('uri', TextType::class, ['required' => false])
                ->add(
                    'routeTarget',
                    TreeSelectType::class,
                    ['root_node' => $this->getRootPath(), 'widget' => 'browser', 'required' => false]
                )
            ->end()
        ;
        $this->addTransformerToField($form->getFormBuilder(), 'parentDocument');
        $this->addTransformerToField($form->getFormBuilder(), 'routeTarget');
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper): void
    {
        $datagridMapper->add('name', NodeNameFilter::class);
    }

    public function getExportFormats(): array
    {
        return [];
    }

    public function toString($object): string
    {
        return $object instanceof Route && $object->getId()
            ? $object->getId()
            : $this->getTranslator()->trans('link_add', [], 'SonataAdminBundle')
        ;
    }
}
