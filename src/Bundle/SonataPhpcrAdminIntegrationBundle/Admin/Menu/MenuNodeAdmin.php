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

use Doctrine\Common\Util\ClassUtils;
use Knp\Menu\ItemInterface as MenuItemInterface;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\DoctrinePHPCRAdminBundle\Form\Type\ChoiceFieldMaskType;
use Symfony\Cmf\Bundle\MenuBundle\Model\MenuNode;
use Symfony\Cmf\Bundle\TreeBrowserBundle\Form\Type\TreeSelectType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

class MenuNodeAdmin extends AbstractMenuNodeAdmin
{
    protected $recursiveBreadcrumbs = true;

    protected function configureListFields(ListMapper $list): void
    {
        parent::configureListFields($list);

        $list
            ->add('uri', 'text')
            ->add('route', 'text')
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function configureFormFields(FormMapper $form): void
    {
        $form
            ->tab('form.tab_general')
                ->with('form.group_location', ['class' => 'col-sm-3'])
                    ->add(
                        'parentDocument',
                        TreeSelectType::class,
                        ['root_node' => $this->menuRoot, 'widget' => 'browser']
                    )
                ->end()
            ->end()
        ;

        $this->addTransformerToField($form->getFormBuilder(), 'parentDocument');

        parent::configureFormFields($form);

        if (!$this->hasParentFieldDescription()) {
            // Add the choice for the node links "target"
            $form
                ->tab('form.tab_general')
                    ->with('form.group_target', ['class' => 'col-sm-6'])
                        ->add('linkType', ChoiceFieldMaskType::class, [
                            'choices' => [
                                'route' => 'route',
                                'uri' => 'uri',
                                'content' => 'content',
                            ],
                            'map' => [
                                'route' => ['link'],
                                'uri' => ['link'],
                                'content' => ['content', TreeSelectType::class],
                            ],
                            'placeholder' => 'auto',
                            'required' => false,
                        ])
                        ->add('link', TextType::class, ['required' => false, 'mapped' => false])
                        ->add(
                            'content',
                            TreeSelectType::class,
                            ['root_node' => $this->contentRoot, 'widget' => 'browser', 'required' => false]
                        )
                    ->end()
                ->end()
            ;

            $this->addTransformerToField($form->getFormBuilder(), 'content');
        }

        $formBuilder = $form->getFormBuilder();

        $formBuilder->addEventListener(FormEvents::POST_SET_DATA, function (FormEvent $event) {
            if (!$event->getForm()->has('link')) {
                return;
            }

            $link = $event->getForm()->get('link');
            $node = $event->getData();

            if (!$node instanceof MenuNode) {
                return;
            }

            switch ($node->getLinkType()) {
                case 'route':
                    $link->setData($node->getRoute());

                    break;

                case 'uri':
                    $link->setData($node->getUri());

                    break;

                case null:
                    $linkType = $event->getForm()->get('linkType');

                    if ($data = $node->getUri()) {
                        $linkType->setData('uri');
                    } else {
                        $data = $node->getRoute();
                        $linkType->setData('route');
                    }

                    $link->setData($data);
            }
        });

        $formBuilder->addEventListener(FormEvents::POST_SUBMIT, function (FormEvent $event) {
            if (!$event->getForm()->has('link')) {
                return;
            }

            $form = $event->getForm();
            $node = $event->getData();

            if (!$node instanceof MenuNode) {
                return;
            }

            $linkType = $form->get('linkType')->getData();
            $link = $form->get('link')->getData();

            switch ($linkType) {
                case 'route':
                    $node->setRoute($link);

                    break;

                case 'uri':
                    $node->setUri($link);

                    break;
            }
        });
    }

    /**
     * {@inheritdoc}
     */
    public function buildBreadcrumbs($action, MenuItemInterface $menu = null)
    {
        $menuNodeNode = parent::buildBreadcrumbs($action, $menu);

        if (!$this->hasSubject()) {
            return $menuNodeNode;
        }

        if ('edit' !== $action || !$this->recursiveBreadcrumbs) {
            return $menuNodeNode;
        }

        $parentDoc = $this->getSubject()->getParentDocument();
        $pool = $this->getConfigurationPool();
        $parentAdmin = $pool->getAdminByClass(ClassUtils::getClass($parentDoc));

        if (null === $parentAdmin) {
            return $menuNodeNode;
        }

        $parentAdmin->setSubject($parentDoc);
        $parentAdmin->setRequest($this->getRequest());
        $parentEditNode = $parentAdmin->buildBreadcrumbs($action, $menu);
        if ($parentAdmin->isGranted('EDIT' && $parentAdmin->hasRoute('edit'))) {
            $parentEditNode->setUri(
                $parentAdmin->generateUrl('edit', [
                    'id' => $this->getUrlsafeIdentifier($parentDoc),
                ])
            );
        }

        $menuNodeNode->setParent(null);
        $current = $parentEditNode->addChild($menuNodeNode);

        return $current;
    }

    public function setRecursiveBreadcrumbs($recursiveBreadcrumbs)
    {
        $this->recursiveBreadcrumbs = (bool) $recursiveBreadcrumbs;
    }
}
