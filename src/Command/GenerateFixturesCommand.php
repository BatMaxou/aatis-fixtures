<?php

namespace Aatis\FixturesBundle\Command;

use Symfony\Component\Yaml\Yaml;
use Symfony\Component\Console\Command\Command;
use Aatis\FixturesBundle\Service\DataGenerator;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Aatis\FixturesBundle\Exception\FileRightsException;
use Aatis\FixturesBundle\Exception\ConfigNotFoundException;

#[AsCommand(
    name: 'aatis:fixtures:generate',
)]
class GenerateFixturesCommand extends Command
{
    private DataGenerator $generator;

    public function __construct(DataGenerator $generator)
    {
        parent::__construct();
        $this->generator = $generator;
    }

    protected function configure(): void
    {
        $this
            ->setDescription('Generate fixtures for the currents models');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

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

        $content = $this->generator->generate($yaml);

        $path = './config/fixtures/config.yaml';
        $dirname = dirname($path);

        if (!is_dir($dirname)) {
            mkdir($dirname, 0o777, true);
        }

        if ($file = fopen($path, 'w')) {
            fwrite($file, Yaml::dump($content, 3));
        } else {
            throw new FileRightsException('Can not access to the content of ./config/fixtures/config.yaml file');
        }

        $io->success('Succeded to create data for currents models into file ./config/fixtures/config.yaml');

        return Command::SUCCESS;
    }
}
