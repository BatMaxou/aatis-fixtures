<?php

namespace App\Command;

use Doctrine\DBAL\Connection;
use App\Service\EntitiesDictionary;
use App\Exception\TruncateException;
use App\Exception\TableNotFoundException;
use App\Exception\ExecuteCommandException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'app:database:refresh'
)]
class DatabaseRefreshCommand extends Command
{
    private $connection;
    private array $tableList;

    public function __construct(Connection $connection, EntitiesDictionary $EntitiesDictionary)
    {
        parent::__construct();
        $this->connection = $connection;
        $this->tableList = $EntitiesDictionary->getEntitiesNames();
    }

    protected function configure(): void
    {
        $this
            ->setDescription('Refresh the database')
            ->setHelp('Drop the database and recreate it with the current schema.')
            ->addOption('force', '-f', InputOption::VALUE_NONE, 'Allows to drop and update the database.')
            ->addArgument('table', InputArgument::IS_ARRAY, 'You can precise one or severals table to truncate in particular.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        if (!$input->getOption('force')) {
            $io->text('Please run the operation with --force to execute');
            $io->caution('All data will be lost!');
            return Command::INVALID;
        }
        // if any table are given
        if (count($input->getArgument('table')) === 0) {
            $drop = $this->getApplication()->find('doctrine:database:drop');
            $create = $this->getApplication()->find('doctrine:database:create');
            $schema = $this->getApplication()->find('doctrine:schema:update');

            if (!$drop || !$create || !$schema) {
                $io->error('Failure when executing the command');
                $io->text('Make sure you have access to the followings doctrine commands :');
                $io->listing([
                    'doctrine:database:drop',
                    'doctrine:database:create',
                    'doctrine:schema:update'
                ]);
                return Command::FAILURE;
            }

            if ($drop->run($input, $output) !== 0) {
                $io->warning('Ignoring the drop database step');
            } else {
                $io->success('Succeded to drop database');
            }

            $inputCreate = new ArrayInput([]);

            if ($create->run($inputCreate, $output) !== 0) throw new ExecuteCommandException('Failure when executing the command : doctrine:database:create');
            $io->success('Succeded to create database');

            if ($schema->run($input, $output) !== 0) throw new ExecuteCommandException('Failure when executing the command : doctrine:schema:update');
            $io->success('Succeded to update the database schema');
        } else {
            $tables = $input->getArgument('table');

            foreach ($tables as $table) {
                $this->truncateTable($table);
                $io->success(sprintf('Succeded to truncate the table %s.', $table));
            }
        }

        return Command::SUCCESS;
    }

    /**
     * Truncate the table given.
     * 
     * @return void
     * 
     * @throws TruncateException
     * @throws TableNotFoundException
     */
    private function truncateTable(string $table): void
    {
        if (in_array($table, $this->tableList)) {
            $delete = $this->connection->prepare('DELETE FROM ' . $table);
            $resetIncrement = $this->connection->prepare('ALTER TABLE ' . $table . ' AUTO_INCREMENT=0');
            try {
                $delete->executeQuery();
                $resetIncrement->executeQuery();
            } catch (\Throwable) {
                throw new TruncateException(sprintf('Error during the truncate of the table %s, it can be due to a foreign key constraint violation.', $table));
            }
        } else {
            throw new TableNotFoundException(sprintf('The table %s does not exist.', $table));
        }
    }
}
