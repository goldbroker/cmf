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

namespace Sonata\DoctrinePHPCRAdminBundle\Builder;

use Doctrine\ODM\PHPCR\Mapping\ClassMetadata;
use Sonata\AdminBundle\Admin\AdminInterface;
use Sonata\AdminBundle\FieldDescription\FieldDescriptionCollection;
use Sonata\AdminBundle\FieldDescription\FieldDescriptionInterface;
use Sonata\AdminBundle\Builder\ListBuilderInterface;
use Sonata\AdminBundle\FieldDescription\TypeGuesserInterface;
use Symfony\Component\Form\Guess\TypeGuess;

class ListBuilder implements ListBuilderInterface
{
    /**
     * @var TypeGuesserInterface
     */
    protected TypeGuesserInterface $guesser;

    /**
     * @var array
     */
    protected array $templates;

    public function __construct(TypeGuesserInterface $guesser, array $templates = [])
    {
        $this->guesser = $guesser;
        $this->templates = $templates;
    }

    /**
     * {@inheritdoc}
     */
    public function getBaseList(array $options = []): FieldDescriptionCollection
    {
        return new FieldDescriptionCollection();
    }

    /**
     * {@inheritdoc}
     */
    public function buildField($type, FieldDescriptionInterface $fieldDescription): void
    {
        if (null === $type) {
            $guessType = $this->guesser->guess($fieldDescription);
            $fieldDescription->setType($guessType instanceof TypeGuess ? $guessType->getType() : null);
        } else {
            $fieldDescription->setType($type);
        }

        $this->fixFieldDescription($fieldDescription);
    }

    /**
     * {@inheritdoc}
     */
    public function addField(FieldDescriptionCollection $list, $type, FieldDescriptionInterface $fieldDescription): void
    {
        $this->buildField($type, $fieldDescription);

        $list->add($fieldDescription);
    }

    /**
     * {@inheritdoc}
     *
     * @throws \RuntimeException if the $fieldDescription does not have a type
     */
    public function fixFieldDescription(FieldDescriptionInterface $fieldDescription): void
    {
        if ('_action' === $fieldDescription->getName() || 'actions' === $fieldDescription->getType()) {
            $this->buildActionFieldDescription($fieldDescription);
        }

        $metadata = null;
        $admin = $fieldDescription->getAdmin();

        if ($admin->getModelManager()->hasMetadata($admin->getClass())) {
            /** @var ClassMetadata $metadata */
            $metadata = $admin->getModelManager()->getMetadata($admin->getClass());

            // TODO sort on parent associations or node name
            $defaultSortable = true;
            if ($metadata->hasAssociation($fieldDescription->getName())
                || $metadata->nodename === $fieldDescription->getName()
            ) {
                $defaultSortable = false;
            }

            // TODO get and set parent association mappings, see
            // https://github.com/sonata-project/SonataDoctrinePhpcrAdminBundle/issues/106
            //$fieldDescription->setParentAssociationMappings($parentAssociationMappings);

            // set the default field mapping
            if (isset($metadata->mappings[$fieldDescription->getName()])) {
                $fieldDescription->setFieldMapping($metadata->mappings[$fieldDescription->getName()]);
                if (false !== $fieldDescription->getOption('sortable')) {
                    $fieldDescription->setOption(
                        'sortable',
                        $fieldDescription->getOption('sortable', $defaultSortable)
                    );
                    $fieldDescription->setOption(
                        'sort_parent_association_mappings',
                        $fieldDescription->getOption(
                            'sort_parent_association_mappings',
                            $fieldDescription->getParentAssociationMappings()
                        )
                    );
                    $fieldDescription->setOption(
                        'sort_field_mapping',
                        $fieldDescription->getOption(
                            'sort_field_mapping',
                            $fieldDescription->getFieldMapping()
                        )
                    );
                }
            }

            // set the default association mapping
            if (isset($metadata->associationMappings[$fieldDescription->getName()])) {
                $fieldDescription->setAssociationMapping($metadata->associationMappings[$fieldDescription->getName()]);
            }

            $fieldDescription->setOption(
                '_sort_order',
                $fieldDescription->getOption('_sort_order', 'ASC')
            );
        }

        if (!$fieldDescription->getType()) {
            throw new \RuntimeException(sprintf(
                'Please define a type for field `%s` in `%s`',
                $fieldDescription->getName(),
                \get_class($admin)
            ));
        }

        $fieldDescription->setOption(
            'code',
            $fieldDescription->getOption('code', $fieldDescription->getName())
        );
        $fieldDescription->setOption(
            'label',
            $fieldDescription->getOption('label', $fieldDescription->getName())
        );

        if (!$fieldDescription->getTemplate()) {
            $fieldDescription->setTemplate($this->getTemplate($fieldDescription->getType()));

            if (ClassMetadata::MANY_TO_ONE === $fieldDescription->getMappingType()) {
                $fieldDescription->setTemplate('@SonataAdmin/CRUD/Association/list_many_to_one.html.twig');
            }

            if (ClassMetadata::MANY_TO_MANY === $fieldDescription->getMappingType()) {
                $fieldDescription->setTemplate('@SonataAdmin/CRUD/Association/list_many_to_many.html.twig');
            }

            if ('child' === $fieldDescription->getMappingType() || 'parent' === $fieldDescription->getMappingType()) {
                $fieldDescription->setTemplate('@SonataAdmin/CRUD/Association/list_one_to_one.html.twig');
            }

            if ('children' === $fieldDescription->getMappingType() || 'referrers' === $fieldDescription->getMappingType()) {
                $fieldDescription->setTemplate('@SonataAdmin/CRUD/Association/list_one_to_many.html.twig');
            }
        }

        $mappingTypes = [
            ClassMetadata::MANY_TO_ONE,
            ClassMetadata::MANY_TO_MANY,
            'children',
            'child',
            'parent',
            'referrers',
        ];

        if ($metadata
            && $metadata->hasAssociation($fieldDescription->getName())
            && \in_array($fieldDescription->getMappingType(), $mappingTypes, true)
        ) {
            $admin->attachAdminClass($fieldDescription);
        }
    }

    /**
     * @return FieldDescriptionInterface
     */
    public function buildActionFieldDescription(FieldDescriptionInterface $fieldDescription): FieldDescriptionInterface
    {
        if (null === $fieldDescription->getTemplate()) {
            $fieldDescription->setTemplate('@SonataAdmin/CRUD/list__action.html.twig');
        }

        if (null === $fieldDescription->getType()) {
            $fieldDescription->setType('actions');
        }

        if (null === $fieldDescription->getOption('name')) {
            $fieldDescription->setOption('name', 'Action');
        }

        if (null === $fieldDescription->getOption('code')) {
            $fieldDescription->setOption('code', 'Action');
        }

        if (null !== $fieldDescription->getOption('actions')) {
            $actions = $fieldDescription->getOption('actions');
            foreach ($actions as $k => $action) {
                if (!isset($action['template'])) {
                    $actions[$k]['template'] = sprintf('@SonataAdmin/CRUD/list__action_%s.html.twig', $k);
                }
            }

            $fieldDescription->setOption('actions', $actions);
        }

        return $fieldDescription;
    }

    /**
     * @param string $type
     *
     * @return string
     */
    private function getTemplate(string $type): ?string
    {
        if (!isset($this->templates[$type])) {
            return null;
        }

        return $this->templates[$type];
    }
}
