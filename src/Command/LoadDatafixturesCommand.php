<?php

namespace App\Command;

use Symfony\Component\Yaml\Yaml;
use App\Service\DatafixturesLoader;
use App\Exception\ConfigNotFoundException;
use App\Exception\ExecuteCommandException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'app:load:datafixtures',
)]
class LoadDatafixturesCommand extends Command
{
    private DatafixturesLoader $loader;

    function __construct(
        DatafixturesLoader $loader
    ) {
        parent::__construct();
        $this->loader = $loader;
    }

    protected function configure(): void
    {
        $this
            ->setDescription('Insert datafixtures')
            ->setHelp('Clear the database and insert the data provided into jeu_essai.yaml.')
            ->addOption('force', '-f', InputOption::VALUE_NONE, 'Allows to clear the database and to insert the datafixtures.')
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

        $def = new InputDefinition();
        $def->addOption(new InputOption('force'));
        $def->addOption(new InputOption('datafixtures'));

        $refreshInput = new ArrayInput([], $def);
        $refreshInput->setOption('force', true);
        $refreshInput->setOption('datafixtures', true);

        $refresh = $this->getApplication()->find('app:database:refresh');
        if ($refresh->execute($input, $output) === 1) throw new ExecuteCommandException('Problem raise during the refresh of the database');

        if (!$yaml = Yaml::parseFile('./datafixtures/config.yaml')) throw new ConfigNotFoundException('File "datafixtures/config.yaml" doesn\'t exist');

        $tables = [];
        if (isset($input->getArguments()['table'])) {
            $tables = $input->getArguments()['table'];
        }

        $this->loader->load($yaml, $io, $tables);

        return Command::SUCCESS;
    }
}
