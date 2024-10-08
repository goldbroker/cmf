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

namespace Sonata\DoctrinePHPCRAdminBundle\Admin;

use Doctrine\ORM\Mapping\ClassMetadataInfo;
use Sonata\AdminBundle\FieldDescription\BaseFieldDescription;

/**
 * {@inheritdoc}
 */
class FieldDescription extends BaseFieldDescription
{
    public function __construct(string $name, ?array $options = [])
    {
        parent::__construct($name, $options);
        $this->parentAssociationMappings = [];
    }

    /**
     * {@inheritdoc}
     *
     * @throws \InvalidArgumentException if the mapping is no array or of an
     *                                   unknown type
     */
    public function setAssociationMapping($associationMapping): void
    {
        if (!\is_array($associationMapping)) {
            throw new \InvalidArgumentException('The association mapping must be an array');
        }

        $this->associationMapping = $associationMapping;

        if (isset($associationMapping['type'])) {
            $this->type = $this->type ?: $associationMapping['type'];
            $this->mappingType = $this->mappingType ?: $associationMapping['type'];
        } else {
            throw new \InvalidArgumentException('Unknown association mapping type');
        }
        $this->fieldName = $associationMapping['fieldName'];
    }

    /**
     * {@inheritdoc}
     */
    public function getTargetEntity(): ?string
    {
        return $this->getTargetModel();
    }

    /**
     * {@inheritdoc}
     *
     * @throws \InvalidArgumentException if the mapping information is not an array
     */
    public function setFieldMapping($fieldMapping): void
    {
        if (!\is_array($fieldMapping)) {
            throw new \InvalidArgumentException('The field mapping must be an array');
        }

        $this->fieldMapping = $fieldMapping;

        $this->type = $this->type ?: $fieldMapping['type'];
        $this->mappingType = $this->mappingType ?: $fieldMapping['type'];
        $this->fieldName = $this->fieldName ?: $fieldMapping['fieldName'];
    }

    /**
     * {@inheritdoc}
     */
    public function isIdentifier(): bool
    {
        return $this->fieldMapping['id'] ?? false;
    }

    /**
     * {@inheritdoc}
     */
    public function getValue($object)
    {
        foreach ($this->parentAssociationMappings as $parentAssociationMapping) {
            $object = $this->getFieldValue($object, $parentAssociationMapping['fieldName']);
        }

        return $this->getFieldValue($object, $this->fieldName);
    }

    /**
     * {@inheritdoc}
     *
     * @throws \InvalidArgumentException if the list of mappings does contain
     *                                   something else than arrays
     */
    public function setParentAssociationMappings(array $parentAssociationMappings): void
    {
        foreach ($parentAssociationMappings as $parentAssociationMapping) {
            if (!\is_array($parentAssociationMapping)) {
                throw new \RuntimeException('An association mapping must be an array');
            }
        }

        $this->parentAssociationMappings = $parentAssociationMappings;
    }

    public function getTargetModel(): ?string
    {
        if (isset($this->associationMapping['targetDocument'])) {
            return $this->associationMapping['targetDocument'];
        }

        if (isset($this->associationMapping['referringDocument'])) {
            return $this->associationMapping['referringDocument'];
        }

        return null;
    }

    public function describesSingleValuedAssociation(): bool
    {
        return \is_int($this->mappingType) && $this->mappingType === ($this->mappingType & ClassMetadataInfo::TO_ONE);
    }

    public function describesCollectionValuedAssociation(): bool
    {
        return \is_int($this->mappingType) && $this->mappingType === ($this->mappingType & ClassMetadataInfo::TO_MANY);
    }
}
