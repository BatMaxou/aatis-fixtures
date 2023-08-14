<?php

namespace App\Service;

use Doctrine\ORM\EntityManagerInterface;

class EntitiesInfosGenerator
{
    private EntityManagerInterface $em;
    // permettre d'exclure des entity qu'on ne veut pas en BDD
    // voir annotation de classe
    // et/ou permettre dans services.yaml
    private array $exclude;

    public function __construct(EntityManagerInterface $em, array $exclude = [])
    {
        $this->em = $em;
        $this->exclude = $exclude;
    }

    private function generateOrderedTableByPriority($allMetadata): array
    {
        $sortedEntities = [];
        $visited = [];

        foreach ($allMetadata as $metadata) {
            $this->visit($metadata, $visited, $sortedEntities);
        }

        return $sortedEntities;
    }

    // le & permet à la fonction recursive de modifier directement le tableau/variable d'origine
    private function visit($metadata, &$visited, &$sortedEntities): void
    {
        $entityName = $metadata->getName();

        if (isset($visited[$entityName])) {
            return;
        }

        $visited[$entityName] = true;

        foreach ($metadata->getAssociationMappings() as $associationMapping) {
            if ($associationMapping['inversedBy']) {
                $targetEntityName = $associationMapping['targetEntity'];
                $targetEntity = $this->em->getClassMetadata($targetEntityName);
                $this->visit($targetEntity, $visited, $sortedEntities);
            }
        }

        $sortedEntities[] = $metadata->getName();
        return;
    }

    /**
     * Generate array infos which contains all the entities of the app witth there namespace and there repository, ordering by there creation priority.
     * 
     * @return array[string]array
     */
    public function generate(): array
    {
        $infos = [];
        $allMetadata = $this->em->getMetadataFactory()->getAllMetadata();

        foreach ($this->generateOrderedTableByPriority($allMetadata) as $fullName) {
            $name = '';
            foreach (str_split(lcfirst(explode('\\', $fullName)[2])) as $letter) {
                if (ctype_upper($letter)) {
                    $name .= '_' . strtolower($letter);
                } else {
                    $name .= $letter;
                }
            }

            $infos[strtolower($name)] = ['class' => $fullName, 'repository' => $this->em->getRepository($fullName)];
        }

        return $infos;
    }
}
