<?php

/*
 * This file is part of the Symfony CMF package.
 *
 * (c) 2011-2017 Symfony CMF
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Cmf\Bundle\SonataPhpcrAdminIntegrationBundle\Admin\Content;

use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\DoctrinePHPCRAdminBundle\Filter\NodeNameFilter;
use Sonata\DoctrinePHPCRAdminBundle\Filter\StringFilter;
use Symfony\Cmf\Bundle\ContentBundle\Model\StaticContentBase;
use Symfony\Cmf\Bundle\SonataPhpcrAdminIntegrationBundle\Admin\AbstractAdmin;
use Symfony\Cmf\Bundle\TreeBrowserBundle\Form\Type\TreeSelectType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class StaticContentAdmin extends AbstractAdmin
{
    /**
     * Configuration, that can be passed to CKEditorType.
     */
    private array $ckEditorConfig = [];

    public function getExportFormats(): array
    {
        return [];
    }

    /**
     * Set configuration for CKEditorType.
     *
     * Documentation: https://symfony.com/doc/master/bundles/FOSCKEditorBundle/usage/config.html
     *
     * @param array $config configuration for CKEditorType
     */
    public function setCkEditorConfig(array $config)
    {
        $this->ckEditorConfig = $config;
    }

    protected function configureListFields(ListMapper $list): void
    {
        $list
            ->addIdentifier('id', 'text')
            ->addIdentifier('title', 'text')
        ;
    }

    protected function configureFormFields(FormMapper $form): void
    {
        $editView = (bool) $this->id($this->getSubject());
        $form
            ->tab('form.tab_general')
                ->with('form.group_content', ['class' => 'col-md-9'])
                    ->add('title', TextType::class)
                    ->add(
                        'body',
                        $this->ckEditorConfig ? CKEditorType::class : TextareaType::class,
                        $this->ckEditorConfig
                    )
                ->end()

                ->with('form.group_location', ['class' => 'col-md-3'])
                    ->ifTrue($editView)
                        ->add('parentDocument', TextType::class, ['disabled' => true])
                    ->ifEnd()
                    ->ifFalse($editView)
                        ->add('parentDocument', TreeSelectType::class, [
                            'widget' => 'browser',
                            'root_node' => $this->getRootPath(),
                        ])
                    ->ifend()

                    ->add('name', TextType::class)
                ->end()
            ->end()
        ;

        $this->addTransformerToField($form->getFormBuilder(), 'parentDocument');
    }

    protected function configureDatagridFilters(DatagridMapper $filter): void
    {
        $filter
            ->add('title', StringFilter::class)
            ->add('name', NodeNameFilter::class)
        ;
    }

    public function toString($object): string
    {
        return $object instanceof StaticContentBase && $object->getTitle()
            ? $object->getTitle()
            : $this->getTranslator()->trans('link_add', [], 'SonataAdminBundle')
        ;
    }
}
