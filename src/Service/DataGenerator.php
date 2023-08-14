<?php

namespace App\Service;

use App\Service\Faker;
use App\Exception\MissingEntityRelationException;
use App\Exception\NotSupportedTypeException;

class DataGenerator
{
    /**
     * Generate fixtures base on the models given.
     * 
     * @return string[]
     */
    public function generate(array $yaml): array
    {
        foreach ($yaml as $tableName => $tableInfos) {
            $tableModel = $tableInfos['model'];
            $iteration = $tableInfos['iteration'];

            for ($i = 0; $i < $iteration; $i++) {
                $data = [];
                foreach ($tableModel as $fakerInfos) {
                    if (isset($fakerInfos['class'])) {
                        if ($fakerInfos['class'] === 'DateTime') {
                            $data[] = strval((new $fakerInfos['class'])->format('Y-m-d H:i:s'));
                        } else {
                            throw new NotSupportedTypeException(sprintf('Type "%s" is not supported.', $fakerInfos['class']));
                        }
                    } else if (isset($fakerInfos['entity'])) {
                        if ($yaml[$fakerInfos['entity']]['iteration'] > 0) {
                            $data[] = Faker::int(['min' => 1, 'max' => $yaml[$fakerInfos['entity']]['iteration']]);
                        } else {
                            throw new MissingEntityRelationException(sprintf('Cannot create entity of instance %s, instance of parent class not found.', $tableName));
                        }
                    } else {
                        $type = $fakerInfos['type'];
                        $data[] = (isset($fakerInfos['attributes'])) ? Faker::$type(...$fakerInfos['attributes']) : Faker::$type();
                    }
                }
                $yaml[$tableName]['data'][$i] = $data;
            }
        }

        return $yaml;
    }
}
