<?php

namespace Aatis\FixturesBundle\Command;

use Symfony\Component\Yaml\Yaml;
use Aatis\FixturesBundle\Service\FixturesLoader;
use Aatis\FixturesBundle\Exception\ConfigNotFoundException;
use Aatis\FixturesBundle\Exception\ExecuteCommandException;
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
    name: 'aatis:load:fixtures',
)]
class LoadFixturesCommand extends Command
{
    private FixturesLoader $loader;

    public function __construct(FixturesLoader $loader)
    {
        parent::__construct();
        $this->loader = $loader;
    }

    protected function configure(): void
    {
        $this
            ->setDescription('Insert fixtures')
            ->setHelp('Clear the database and insert the data provided into jeu_essai.yaml.')
            ->addOption('force', '-f', InputOption::VALUE_NONE, 'Allows to clear the database and to insert the fixtures.')
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

        $refreshInput = new ArrayInput([], $def);
        $refreshInput->setOption('force', true);

        $app = $this->getApplication();
        if (null !== $app) {
            $refresh = $app->find('aatis:database:refresh');
            if (1 === $refresh->execute($input, $output)) {
                throw new ExecuteCommandException('Problem raise during the refresh of the database');
            }
        }

        if (!is_readable('./config/fixtures/config.yaml')) {
            throw new ConfigNotFoundException('File "./config/fixtures/config.yaml" doesn\'t exist');
        }

        /**
         * @var array<string, array{
         *      iteration: 0,
         *      model: array<array{
         *          class: string
         *      }|array{
         *          entity: string
         *      }|array{
         *          type: string
         *      }>,
         *      data: array{}|array<int, array<int, int|string>>
         * }> $yaml
         */
        $yaml = Yaml::parseFile('./config/fixtures/config.yaml') ?? throw new ConfigNotFoundException('Config in "./config/fixtures/config.yaml" not found, maybe your file is empty');

        if (isset($input->getArguments()['table'])) {
            /**
             * @var string[] $tables
             */
            $tables = $input->getArguments()['table'];
        }

        $this->loader->load($yaml, $io, $tables ?? []);

        return Command::SUCCESS;
    }
}
