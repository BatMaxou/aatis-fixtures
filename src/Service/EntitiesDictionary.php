<?php

namespace App\Service;

use ReflectionClass;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

class EntitiesDictionary
{
    private array $infos;

    public function __construct(
        EntitiesInfosGenerator $generator
    ) {
        $this->infos = $generator->generate();
    }

    /**
     * Generate array infos which contains all the entities of the app witth there namespace and there repository, ordering by there creation priority.
     * 
     * @return array[string]array
     */
    public function getInfos(): array
    {
        return $this->infos;
    }

    /**
     * Return an array where the keys are the name of the your entities in snake_case, and the value is the namespace of this entity ordering by there creation priority.
     * 
     * @return string[]
     */
    public function getEntities(): array
    {
        $entities = [];

        foreach ($this->infos as $key => $value) {
            $entities[$key] = ['class' => $value['class']];
        }

        return $entities;
    }

    /**
     * Return an array with the name of your entities in snake_case, ordering by there creation priority.
     * 
     * @return string[]
     */
    public function getEntitiesNames(): array
    {
        return array_keys($this->infos);
    }

    /**
     * Return the properties of a given entity name (the name must be in snake_case).
     * 
     * @param string $entityName The name of the target entity in snake_case.
     * 
     * @return string[]|null
     */
    public function getProperties(string $entityName): ?array
    {
        if (!array_key_exists($entityName, $this->infos)) {
            return null;
        }

        $reflection = new ReflectionClass($this->infos[$entityName]['class']);
        $properties = $reflection->getProperties();

        $accurateProperties = [];

        foreach ($properties as $property) {
            if ($property->getName() !== 'id') {
                if ($property->getType()->getName() !== 'Doctrine\Common\Collections\Collection') {
                    $accurateProperties[$property->getName()] = str_replace('Interface', '', $property->getType()->getName());
                }
            }
        }

        return $accurateProperties;
    }

    /**
     * Return an array where the keys are the name of the your entities in snake_case and the value is the repository of this entity, ordering by there creation priority.
     * 
     * @return ServiceEntityRepository[]
     */
    public function getRepositories(): array
    {
        $repositories = [];

        foreach ($this->infos as $key => $value) {
            $repositories[$key] = $value['repository'];
        }

        return $repositories;
    }
}
