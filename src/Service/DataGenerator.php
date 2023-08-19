<?php

namespace Aatis\FixturesBundle\Service;

use Aatis\FixturesBundle\Exception\MissingEntityRelationException;
use Aatis\FixturesBundle\Exception\NotSupportedTypeException;

/**
 * @phpstan-type YamlType array<string, array{
 *      iteration: int,
 *      model: array<array{
 *          class: string
 *      }|array{
 *          entity: string
 *      }|array{
 *          type: string,
 *          parameters?: array<int, int|string|array<int|string, int|string>>
 *      }>,
 *      data: array{}|array<int, array<int, int|string>>
 * }>
 */
class DataGenerator
{
    /**
     * Generate fixtures base on the models given.
     *
     * @param YamlType $yaml
     *
     * @return YamlType
     *
     * @throws NotSupportedTypeException
     * @throws MissingEntityRelationException
     */
    public function generate(array $yaml)
    {
        foreach ($yaml as $tableName => $tableInfos) {
            $tableModel = $tableInfos['model'];
            $iteration = $tableInfos['iteration'];

            for ($i = 0; $i < $iteration; ++$i) {
                $data = [];
                foreach ($tableModel as $fakerInfos) {
                    if (isset($fakerInfos['class'])) {
                        if ('DateTime' === $fakerInfos['class']) {
                            $data[] = strval((new $fakerInfos['class']())->format('Y-m-d H:i:s'));
                        } else {
                            throw new NotSupportedTypeException(sprintf('Type "%s" is not supported.', $fakerInfos['class']));
                        }
                    } elseif (isset($fakerInfos['entity'])) {
                        if ($yaml[$fakerInfos['entity']]['iteration'] > 0) {
                            $data[] = Faker::int(['min' => 1, 'max' => $yaml[$fakerInfos['entity']]['iteration']]);
                        } else {
                            throw new MissingEntityRelationException(sprintf('Cannot create entity of instance %s, instance of parent class not found.', $tableName));
                        }
                    } elseif (isset($fakerInfos['type'])) {
                        $type = $fakerInfos['type'];
                        $data[] = (isset($fakerInfos['parameters'])) ? Faker::$type(...$fakerInfos['parameters']) : Faker::$type();
                    }
                }
                $yaml[$tableName]['data'][$i] = $data;
            }
        }

        return $yaml;
    }
}
