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

use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\Form\Type\CollectionType;
use Symfony\Cmf\Bundle\BlockBundle\Doctrine\Phpcr\SlideshowBlock;
use Symfony\Component\Form\Extension\Core\Type\TextType;

/**
 * @author Horner
 */
class SlideshowBlockAdmin extends AbstractBlockAdmin
{
    /**
     * Service name of the sonata_type_collection service to embed.
     *
     * @var string
     */
    protected $embeddedAdminCode;

    /**
     * Configure the service name (admin_code) of the admin service for the embedded slides.
     *
     * @param string $adminCode
     */
    public function setEmbeddedSlidesAdmin($adminCode)
    {
        $this->embeddedAdminCode = $adminCode;
    }

    /**
     * {@inheritdoc}
     */
    protected function configureListFields(ListMapper $list): void
    {
        parent::configureListFields($list);
        $list
            ->addIdentifier('id', 'text')
            ->add('title', 'text')
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function configureFormFields(FormMapper $form): void
    {
        if (!$this->hasParentFieldDescription()) {
            parent::configureFormFields($form);
        }

        $form
            ->tab('form.tab_general')
                ->with('form.group_block', !$this->hasParentFieldDescription()
                    ? ['class' => 'col-md-9']
                    : []
                )
                    ->add('title', TextType::class, ['required' => false])
                    ->add('children', CollectionType::class,
                        [],
                        [
                            'edit' => 'inline',
                            'inline' => 'table',
                            'admin_code' => $this->embeddedAdminCode,
                            'sortable' => 'position',
                        ])
                ->end()
            ->end()
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function prePersist($slideshow): void
    {
        foreach ($slideshow->getChildren() as $child) {
            $child->setName($this->generateName());
        }
    }

    /**
     * {@inheritdoc}
     */
    public function preUpdate($slideshow): void
    {
        foreach ($slideshow->getChildren() as $child) {
            if (!$this->getModelManager()->getNormalizedIdentifier($child)) {
                $child->setName($this->generateName());
            }
        }
    }

    /**
     * Generate a most likely unique name.
     *
     * TODO: have blocks use the autoname annotation - https://github.com/symfony-cmf/BlockBundle/issues/149
     *
     * @return string
     */
    private function generateName()
    {
        return 'child_'.time().'_'.rand();
    }

    public function toString($object): string
    {
        return $object instanceof SlideshowBlock && $object->getTitle()
            ? $object->getTitle()
            : parent::toString($object)
        ;
    }
}
