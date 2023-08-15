<?php

namespace Aatis\FixturesBundle\Service;

class ModelsGenerator
{
    private EntitiesDictionary $EntitiesDictionary;

    public function __construct(EntitiesDictionary $EntitiesDictionary)
    {
        $this->EntitiesDictionary = $EntitiesDictionary;
    }

    /**
     * Generate an array of arrays where each one represent one of your entities with the infos of each of there properties.
     *
     * @return array[string]array
     */
    public function generate(): array
    {
        $content = [];
        $entitiesNames = $this->EntitiesDictionary->getEntitiesNames();

        foreach ($entitiesNames as $name) {
            $model = [];

            foreach ($this->EntitiesDictionary->getProperties($name) as $propertyName => $type) {
                if (str_starts_with($type, 'App\\Entity\\')) {
                    $model[$propertyName] = ['entity' => lcfirst(str_replace('App\\Entity\\', '', $type))];
                } elseif (ctype_upper($type[0])) {
                    $model[$propertyName] = ['class' => $type];
                } else {
                    $model[$propertyName] = ['type' => $type];
                }
            }

            $content[$name] = [
                'iteration' => 0,
                'model' => $model,
                'data' => [],
            ];
        }

        return $content;
    }
}
