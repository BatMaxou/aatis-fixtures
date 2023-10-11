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
     * @var array<string, array<int, int|string>>
     */
    private array $uniqueData = [];

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
                foreach ($tableModel as $attributeName => $fakerInfos) {
                    if (isset($fakerInfos['class'])) {
                        if ($this->isUnique($fakerInfos)) {
                            $data[] = $this->handleUnique($attributeName, 'generateDateTime', [$fakerInfos]);
                        } else {
                            $data[] = $this->generateDateTime($fakerInfos);
                        }
                    } elseif (isset($fakerInfos['entity'])) {
                        if ($this->isUnique($fakerInfos)) {
                            $data[] = $this->handleUnique($attributeName, 'generateRelation', [$yaml[$fakerInfos['entity']]['iteration'], $tableName], ['iteration' => $iteration]);
                        } else {
                            $data[] = $this->generateRelation($yaml[$fakerInfos['entity']]['iteration'], $tableName);
                        }
                    } elseif (isset($fakerInfos['type'])) {
                        $type = $fakerInfos['type'];
                        if ($this->isUnique($fakerInfos)) {
                            $data[] = $this->handleUnique($attributeName, 'generateOtherData', [$fakerInfos, $type]);
                        } else {
                            $data[] = $this->generateOtherData($fakerInfos, $type);
                        }
                    }
                }
                $yaml[$tableName]['data'][$i] = $data;
            }
            $this->uniqueData = [];
        }

        return $yaml;
    }

    /**
     * Generate a DateTime.
     */
    private function generateDateTime(array $fakerInfos): string
    {
        if ('DateTime' === $fakerInfos['class']) {
            return strval((new $fakerInfos['class']())->format('Y-m-d H:i:s'));
        } else {
            throw new NotSupportedTypeException(sprintf('Type "%s" is not supported.', $fakerInfos['class']));
        }
    }

    /**
     * Generate a the index of the related entity.
     */
    private function generateRelation(int $relatedEntityIteration, string $tableName): int
    {
        if ($relatedEntityIteration > 0) {
            return Faker::int(['min' => 1, 'max' => $relatedEntityIteration]);
        } else {
            throw new MissingEntityRelationException(sprintf('Cannot create entity of instance %s, instance of parent class not found.', $tableName));
        }
    }

    /**
     * Generate other data with faker.
     *
     * @param string $type The name of the faker method
     */
    private function generateOtherData(array $fakerInfos, string $type): mixed
    {
        return (isset($fakerInfos['parameters'])) ? Faker::$type(...$fakerInfos['parameters']) : Faker::$type();
    }

    private function isUnique(array $fakerInfos): bool
    {
        return isset($fakerInfos['unique']) && $fakerInfos['unique'];
    }

    private function handleUnique(string $attributeName, string $callback, array $args, $options = [])
    {
        if (!isset($this->uniqueData[$attributeName])) {
            $this->uniqueData[$attributeName] = [];
        }

        $data = call_user_func_array([$this, $callback], $args);
        while (in_array($data, $this->uniqueData[$attributeName])) {
            $data = call_user_func_array([$this, $callback], $args);
        }
        $this->uniqueData[$attributeName][] = $data;

        return $data;
    }
}
