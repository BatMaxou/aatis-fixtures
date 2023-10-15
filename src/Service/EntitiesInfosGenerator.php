<?php

namespace Aatis\FixturesBundle\Service;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\ClassMetadataInfo;

class EntitiesInfosGenerator
{
    private EntityManagerInterface $em;
    // permettre d'exclure des entity qu'on ne veut pas en BDD
    // voir annotation de classe
    // et/ou permettre dans services.yaml
    // private array $exclude;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
        // $this->exclude = $exclude;
    }

    /**
     * @return string[]
     */
    private function generateOrderedTableByPriority(): array
    {
        $allMetadata = $this->em->getMetadataFactory()->getAllMetadata();
        $sortedEntities = [];
        $visited = [];

        foreach ($allMetadata as $metadata) {
            $this->visit($metadata, $visited, $sortedEntities);
        }

        return $sortedEntities;
    }

    /**
     * @template T of object
     *
     * @param ClassMetadataInfo<T> $metadata
     * @param array<string, bool> $visited
     * @param string[] $sortedEntities
     */
    private function visit(ClassMetadataInfo $metadata, array &$visited, array &$sortedEntities): void
    {
        $fullName = $metadata->getName();

        if (isset($visited[$fullName])) {
            return;
        }

        $visited[$fullName] = true;

        foreach ($metadata->getAssociationMappings() as $associationMapping) {
            $reflexion = new \ReflectionClass($fullName);
            $property = $reflexion->getProperty($associationMapping['fieldName']);
            $attributes = $property->getAttributes();
            foreach ($attributes as $attribute) {
                $attributeName = $attribute->getName();
                if (
                    'Doctrine\ORM\Mapping\ManyToOne' === $attributeName
                    || 'Doctrine\ORM\Mapping\OneToOne' === $attributeName
                ) {
                    $targetFullName = $associationMapping['targetEntity'];
                    $targetEntity = $this->em->getClassMetadata($targetFullName);
                    $this->visit($targetEntity, $visited, $sortedEntities);
                }
            }
        }

        $sortedEntities[] = $metadata->getName();

        return;
    }

    /**
     * Generate array infos which contains all the entities of the app with there namespace, ordering by there creation priority.
     *
     * @return array<string, class-string<object>>
     */
    public function generate(): array
    {
        $infos = [];

        /**
         * @var class-string<object> $fullName
         */
        foreach ($this->generateOrderedTableByPriority() as $fullName) {
            $name = '';
            $explode = explode('\\', $fullName);
            foreach (str_split(lcfirst(end($explode))) as $letter) {
                if (ctype_upper($letter)) {
                    $name .= '_'.strtolower($letter);
                } else {
                    $name .= $letter;
                }
            }

            $infos[$name] = $fullName;
        }

        return $infos;
    }
}
