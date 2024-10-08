<?php

/*
 * This file is part of the Symfony CMF package.
 *
 * (c) 2011-2017 Symfony CMF
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Cmf\Bundle\SonataPhpcrAdminIntegrationBundle\Admin\Routing\Extension;

use Sonata\AdminBundle\Admin\AbstractAdminExtension;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\Form\Type\CollectionType;

/**
 * Admin extension to add routes tab to content implementing the
 * RouteReferrersInterface.
 *
 * @author David Buchmann <mail@davidbu.ch>
 */
class RouteReferrersExtension extends AbstractAdminExtension
{
    private $formGroup;

    private $formTab;

    public function __construct($formGroup = 'form.group_routes', $formTab = 'form.tab_routes')
    {
        $this->formGroup = $formGroup;
        $this->formTab = $formTab;
    }

    public function configureFormFields(FormMapper $form): void
    {
        if ($form->hasOpenTab()) {
            $form->end();
        }

        $form
            ->tab($this->formTab)
                ->with($this->formGroup, ['translation_domain' => 'CmfSonataPhpcrAdminIntegrationBundle'])
                    ->add(
                        'routes',
                        CollectionType::class,
                        ['label' => false],
                        [
                            'edit' => 'inline',
                            'inline' => 'table',
                        ]
                    )
                ->end()
            ->end()
        ;
    }
}
