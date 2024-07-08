<?php

/*
 * This file is part of the Symfony CMF package.
 *
 * (c) 2011-2017 Symfony CMF
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Cmf\Bundle\SonataPhpcrAdminIntegrationBundle\Admin\Menu;

use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use Symfony\Cmf\Bundle\ContentBundle\Doctrine\Phpcr\StaticContent;
use Symfony\Cmf\Bundle\MenuBundle\Model\MenuNodeBase;
use Symfony\Cmf\Bundle\SonataPhpcrAdminIntegrationBundle\Admin\AbstractAdmin;
use Symfony\Component\Form\Extension\Core\Type\TextType;

/**
 * Common base admin for Menu and MenuNode.
 */
abstract class AbstractMenuNodeAdmin extends AbstractAdmin
{
    /**
     * @var string
     */
    protected $contentRoot;

    /**
     * @var string
     */
    protected $menuRoot;

    /**
     * @var string
     */
    protected $translationDomain = 'CmfSonataPhpcrAdminIntegrationBundle';

    protected function configureListFields(ListMapper $list): void
    {
        $list
            ->addIdentifier('name', 'text')
            ->add('label', 'text')
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function configureFormFields(FormMapper $form): void
    {
        $form
            ->tab('form.tab_general')
                ->with('form.group_location', ['class' => 'col-md-3'])
                    ->add('name', TextType::class)
                    ->add('label', TextType::class)
                ->end()
            ->end()
        ;
    }

    protected function configureShowFields(ShowMapper $showMapper): void
    {
        $showMapper
            ->add('id')
            ->add('name')
            ->add('label')
            ->add('uri')
            ->add('content.title')
        ;
    }

    public function getExportFormats(): array
    {
        return [];
    }

    public function setContentRoot($contentRoot)
    {
        $this->contentRoot = $contentRoot;
    }

    public function setMenuRoot($menuRoot)
    {
        $this->menuRoot = $menuRoot;
    }

    public function setContentTreeBlock($contentTreeBlock)
    {
        $this->contentTreeBlock = $contentTreeBlock;
    }

    public function toString($object): string
    {
        if ($object instanceof MenuNodeBase && $object->getLabel()) {
            return $object->getLabel();
        }

        return $this->trans('link_add', [], 'SonataAdminBundle');
    }
}
