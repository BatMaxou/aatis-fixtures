<?php

namespace Aatis\FixturesBundle\Service;

class EntitiesDictionary
{
    /**
     * @var array<string, class-string<object>>
     */
    private array $infos;

    public function __construct(EntitiesInfosGenerator $generator)
    {
        $this->infos = $generator->generate();
    }

    /**
     * Generate array infos which contains all the entities of the app witth there namespace and there repository, ordering by there creation priority.
     *
     * @return array<string, class-string<object>>
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
            $entities[$key] = $value;
        }

        return $entities;
    }

    /**
     * Return the ::class of the given entity name in snake_case.
     *
     * @return class-string<object>
     */
    public function getEntity(string $className): string
    {
        return $this->infos[$className];
    }

    /**
     * Return the snake_case of the given class name.
     */
    public function getSnakeCase(string $class): string
    {
        return array_flip($this->infos)[$class];
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
     * @param string $entityName the name of the target entity in snake_case
     *
     * @return array{}|array<non-empty-string, array{}|array{
     *    type: string,
     *    unique?: bool,
     * }>
     */
    public function getProperties(string $entityName): array
    {
        if (!array_key_exists($entityName, $this->infos)) {
            return [];
        }

        $reflection = new \ReflectionClass($this->infos[$entityName]);
        $properties = $reflection->getProperties();
        $accurateProperties = [];
        foreach ($properties as $property) {
            $isColumnInDatabase = false;
            $isJson = false;

            foreach ($property->getAttributes() as $attribute) {
                if (!str_starts_with($attribute->getName(), 'Doctrine\ORM\Mapping')) {
                    continue;
                }

                if (isset($attribute->getArguments()['type']) && 'json' === $attribute->getArguments()['type']) {
                    $isJson = true;
                }

                $isColumnInDatabase = true;
            }

            if (!$isColumnInDatabase) {
                continue;
            }

            $propertyName = $property->getName();
            if ('id' !== $propertyName) {
                if ($isJson) {
                    $accurateProperties[$propertyName]['type'] = 'json';

                    continue;
                }

                /**
                 * @var \ReflectionNamedType $propertyType
                 */
                $propertyType = $property->getType();
                $propertyTypeName = $propertyType->getName();

                foreach ($property->getAttributes() as $attribute) {
                    if (
                        (in_array('unique', array_keys($attribute->getArguments())) && $attribute->getArguments()['unique'])
                        || 'Doctrine\ORM\Mapping\OneToOne' === $attribute->getName()
                    ) {
                        $accurateProperties[$propertyName]['unique'] = true;
                    }
                }

                if (
                    'Doctrine\Common\Collections\Collection' !== $propertyTypeName
                    && 'array' !== $propertyTypeName
                ) {
                    $accurateProperties[$propertyName]['type'] = str_replace('Interface', '', $propertyTypeName);
                }
            }
        }

        return $accurateProperties;
    }
}
