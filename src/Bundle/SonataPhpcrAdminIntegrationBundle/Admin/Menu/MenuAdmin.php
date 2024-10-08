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

use Sonata\AdminBundle\Form\FormMapper;
use Sonata\DoctrinePHPCRAdminBundle\Form\Type\TreeManagerType;
use Symfony\Cmf\Bundle\MenuBundle\Doctrine\Phpcr\Menu;

class MenuAdmin extends AbstractMenuNodeAdmin
{
    /**
     * {@inheritdoc}
     */
    protected function configureFormFields(FormMapper $form): void
    {
        parent::configureFormFields($form);

        $subject = $this->getSubject();
        $isNew = null === $subject->getId();

        if (!$isNew) {
            $form
                ->tab('form.tab_general')
                    ->with('form.group_items', ['class' => 'col-md-6'])
                        ->add('children', TreeManagerType::class, [
                            'root' => $subject->getId(),
                            'edit_in_overlay' => false,
                            'create_in_overlay' => false,
                            'delete_in_overlay' => false,
                        ], [
                            'help' => 'help.help_items',
                        ])
                    ->end()
                ->end()
            ;
        }
    }

    public function alterNewInstance(object $object): void
    {
        $object->setParentDocument($this->getModelManager()->find(null, $this->menuRoot));
    }
}
