<?php

/*
 * This file is part of the Symfony CMF package.
 *
 * (c) 2011-2017 Symfony CMF
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Cmf\Bundle\SonataPhpcrAdminIntegrationBundle\Admin\Menu\Extension;

use Sonata\AdminBundle\Admin\AbstractAdminExtension;
use Sonata\AdminBundle\Form\FormMapper;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;

/**
 * Admin extension for editing menu options
 * implementing MenuOptionsInterface.
 *
 * @author Mojtaba Koosej <mkoosej@gmail.com>
 */
class MenuOptionsExtension extends AbstractAdminExtension
{
    protected string $formGroup;

    protected string $formTab;

    /**
     * @param string $formGroup - group to use for form mapper
     */
    public function __construct(string $formGroup = 'form.group_menu_options', string $formTab = 'form.tab_general')
    {
        $this->formGroup = $formGroup;
        $this->formTab = $formTab;
    }

    /**
     * {@inheritdoc}
     */
    public function configureFormFields(FormMapper $form): void
    {
        if ($form->hasOpenTab()) {
            $form->end();
        }

        $form
            ->tab($this->formTab, 'form.tab_general' === $this->formTab
                ? ['translation_domain' => 'CmfSonataPhpcrAdminIntegrationBundle']
                : []
            )
                ->with($this->formGroup, 'form.group_menu_options' === $this->formGroup
                    ? ['class' => 'col-md-3', 'translation_domain' => 'CmfSonataPhpcrAdminIntegrationBundle']
                    : ['class' => 'col-md-3']
                )
                    ->add('display', CheckboxType::class, ['required' => false], ['help' => 'form.help_display'])
                    ->add('displayChildren', CheckboxType::class, ['required' => false], ['help' => 'form.help_display_children'])
                ->end()
            ->end()
        ;
    }
}
