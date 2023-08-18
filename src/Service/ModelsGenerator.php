<?php

namespace Aatis\FixturesBundle\Service;

use Aatis\FixturesBundle\Service\EntitiesDictionary;

class ModelsGenerator
{
    private EntitiesDictionary $entitiesDictionary;

    public function __construct(EntitiesDictionary $entitiesDictionary)
    {
        $this->entitiesDictionary = $entitiesDictionary;
    }

    /**
     * Generate an array of arrays where each one represent one of your entities with the infos of each of there properties.
     *
     * @return array<string, array{
     *      iteration: 0,
     *      model: array<array{
     *          class: string
     *      }|array{
     *          entity: string
     *      }|array{
     *          type: string
     *      }>,
     *      data: array{}
     * }>
     */
    public function generate(): array
    {
        $content = [];
        $entitiesNames = $this->entitiesDictionary->getEntitiesNames();

        foreach ($entitiesNames as $name) {
            $model = [];

            foreach ($this->entitiesDictionary->getProperties($name) as $propertyName => $type) {
                if (preg_match('/^(([a-zA-Z0-9]|\\\)*)\\\Entity\\\(([a-zA-Z0-9]|\\\)*)$/', $type, $matches)) {
                    $model[$propertyName] = ['entity' => $this->entitiesDictionary->getSnakeCase($type)];
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
