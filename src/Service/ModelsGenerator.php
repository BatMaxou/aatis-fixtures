<?php

namespace Aatis\FixturesBundle\Service;

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


            foreach ($this->entitiesDictionary->getProperties($name) as $propertyName => $arguments) {
                if (isset($arguments['unique']) && $arguments['unique']) {
                    $model[$propertyName]['unique'] = true;
                }

                if (preg_match('/^(([a-zA-Z0-9]|\\\)*)\\\Entity\\\(([a-zA-Z0-9]|\\\)*)$/', $arguments['type'], $matches)) {
                    $model[$propertyName]['entity'] = $this->entitiesDictionary->getSnakeCase($arguments['type']);
                } elseif (ctype_upper($arguments['type'][0])) {
                    $model[$propertyName]['class'] = $arguments['type'];
                } else {
                    $model[$propertyName]['type'] = $arguments['type'];
                }
            }

            $content[$name] = [
                'iteration' => 5,
                'model' => $model,
                'data' => [],
            ];
        }

        return $content;
    }
}
