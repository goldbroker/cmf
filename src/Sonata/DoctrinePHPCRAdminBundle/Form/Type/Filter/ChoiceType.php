<?php

declare(strict_types=1);

/*
 * This file is part of the Sonata Project package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\DoctrinePHPCRAdminBundle\Form\Type\Filter;

use Sonata\AdminBundle\Form\Type\Operator\ContainsOperatorType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType as FormChoiceType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType as SymfonyChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\Translation\TranslatorInterface;

class ChoiceType extends AbstractType
{
    public const TYPE_CONTAINS_WORDS = 4;

    private TranslatorInterface $translator;

    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    /**
     * NEXT_MAJOR: remove this method.
     */
    public function getName(): string
    {
        return $this->getBlockPrefix();
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix(): string
    {
        return 'doctrine_phpcr_type_filter_choice';
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $choices = [
            $this->translator->trans('label_type_contains', [], 'SonataAdminBundle') => ContainsOperatorType::TYPE_CONTAINS,
            $this->translator->trans('label_type_not_contains', [], 'SonataAdminBundle') => ContainsOperatorType::TYPE_NOT_CONTAINS,
            $this->translator->trans('label_type_equals', [], 'SonataAdminBundle') => ContainsOperatorType::TYPE_EQUAL,
            $this->translator->trans('label_type_contains_words', [], 'SonataDoctrinePHPCRAdmin') => self::TYPE_CONTAINS_WORDS,
        ];

        $builder
            ->add('type', SymfonyChoiceType::class, [
                'choices' => $choices,
                'required' => false,
            ])
            ->add('value', $options['field_type'], array_merge(['required' => false], $options['field_options']));
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'field_type' => FormChoiceType::class,
            'field_options' => [],
            'operator_type' => ContainsOperatorType::class,
            'operator_options' => [],
        ]);
    }
}
