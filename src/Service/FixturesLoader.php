<?php

namespace Aatis\FixturesBundle\Service;

use Symfony\Component\Console\Style\SymfonyStyle;
use Aatis\FixturesBundle\Exception\ClassNotFoundExcepton;
use Aatis\FixturesBundle\Exception\ClassNotFoundException;
use Aatis\FixturesBundle\Exception\EntityNotFoundException;
use Aatis\FixturesBundle\Exception\MissingArgumentException;

class FixturesLoader
{
    private array $repositories;

    public function __construct(EntitiesDictionary $EntitiesDictionary)
    {
        $this->repositories = $EntitiesDictionary->getRepositories();
    }

    /**
     * Parse and load fixtures of a YAML file into the database.
     *
     *
     * @throws ClassNotFoundException
     * @throws EntityNotFoundException
     * @throws MissingArgumentException
     */
    public function load(array $yaml, SymfonyStyle $io, array $tables = []): bool
    {
        $tableModel = null;
        // pour chaque TABLE
        foreach ($yaml as $tableName => $tableInfos) {
            // si la table est demandée ou si toutes les tables sont demandées
            if (0 === count($tables) || in_array($tableName, $tables)) {
                $compt = 0;
                // récupération du MODEL de la table
                $tableModel = $tableInfos['model'];

                // pour chaque INSERT
                foreach ($tableInfos['data'] as $data) {
                    // créer une nouvelle entité
                    $entityName = null;
                    foreach (explode('_', $tableName) as $subName) {
                        $entityName .= ucfirst($subName);
                    }
                    $entity = new ('App\\Entity\\'.$entityName)();

                    // pour chaque COLONES
                    $indexColumn = 0;
                    foreach ($tableModel as $column => $type) {
                        if (isset($data[$indexColumn])) {
                            // gérer les datetime ou autres class basiques
                            // ou gérer les clés étrangères
                            if (isset($type['class'])) {
                                if (null === $data[$indexColumn]) {
                                    $value = null;
                                } else {
                                    try {
                                        $value = new $type['class']($data[$indexColumn]);
                                    } catch (\Throwable) {
                                        throw new ClassNotFoundExcepton(sprintf('"%s" class does not exist or format does not fit', $type['class']));
                                    }
                                }
                            } elseif (isset($type['entity'])) {
                                if (null === $data[$indexColumn]) {
                                    $value = null;
                                } else {
                                    $value = $this->repositories[$type['entity']]->find($data[$indexColumn]);
                                    if (null === $value) {
                                        throw new EntityNotFoundException(sprintf('Id "%d" does not match an existing %s.', $data['indexColumn'], $type['entity']));
                                    }
                                }
                            } else {
                                $value = $data[$indexColumn];
                            }
                            $setter = 'set'.ucfirst($column);
                            $entity->$setter($value);
                            ++$indexColumn;
                        } else {
                            throw new MissingArgumentException(sprintf('Missing argument n°%d in fixtures of table %s', $indexColumn + 1, $tableName));
                        }
                    }
                    $this->repositories[$tableName]->save($entity, true);
                    ++$compt;
                }
                $io->info($compt.' row(s) inserted into : '.$tableName);
            }
        }

        return true;
    }
}
