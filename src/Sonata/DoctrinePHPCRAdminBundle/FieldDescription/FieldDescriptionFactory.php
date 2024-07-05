<?php

namespace Sonata\DoctrinePHPCRAdminBundle\FieldDescription;

use Doctrine\Bundle\PHPCRBundle\ManagerRegistry;
use Doctrine\ODM\PHPCR\DocumentManagerInterface;
use Doctrine\Persistence\Mapping\ClassMetadata;
use Sonata\AdminBundle\FieldDescription\FieldDescriptionFactoryInterface;
use Sonata\AdminBundle\FieldDescription\FieldDescriptionInterface;
use Sonata\DoctrinePHPCRAdminBundle\Admin\FieldDescription;

class FieldDescriptionFactory implements FieldDescriptionFactoryInterface
{
    private ManagerRegistry $registry;

    public function __construct(ManagerRegistry $registry)
    {
        $this->registry = $registry;
    }

    public function create(string $class, string $name, array $options = []): FieldDescriptionInterface
    {
        if (!isset($options['route']['name'])) {
            $options['route']['name'] = 'show';
        }

        if (!isset($options['route']['parameters'])) {
            $options['route']['parameters'] = [];
        }

        return new FieldDescription(
            $name,
            $options
        );
    }

    private function getParentMetadataForProperty(string $baseClass, string $propertyFullName): array
    {
        $nameElements = explode('.', $propertyFullName);
        $lastPropertyName = array_pop($nameElements);
        $class = $baseClass;
        $parentAssociationMappings = [];

        foreach ($nameElements as $nameElement) {
            $metadata = $this->getMetadata($class);

            if (isset($metadata->associationMappings[$nameElement])) {
                $parentAssociationMappings[] = $metadata->associationMappings[$nameElement];
                $class = $metadata->getAssociationTargetClass($nameElement);

                continue;
            }

            break;
        }

        $properties = \array_slice($nameElements, \count($parentAssociationMappings));
        $properties[] = $lastPropertyName;

        return [
            $this->getMetadata($class),
            implode('.', $properties),
            $parentAssociationMappings,
        ];
    }

    /**
     * @param class-string $class
     */
    private function getMetadata(string $class): ClassMetadata
    {
        return $this->getDocumentManager($class)->getMetadataFactory()->getMetadataFor($class);
    }

    /**
     * @param class-string $class
     *
     * @throw \UnexpectedValueException
     */
    private function getDocumentManager(string $class): DocumentManagerInterface
    {
        $dm = $this->registry->getManagerForClass($class);

        if (!$dm instanceof DocumentManagerInterface) {
            throw new \UnexpectedValueException(sprintf('No entity manager defined for class "%s".', $class));
        }

        return $dm;
    }
}