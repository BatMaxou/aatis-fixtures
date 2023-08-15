<?php

namespace Aatis\FixturesBundle\Command;

use Aatis\FixturesBundle\Service\DataGenerator;
use Symfony\Component\Yaml\Yaml;
use Aatis\FixturesBundle\Exception\ConfigNotFoundException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

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

        if (!$yaml = Yaml::parseFile('./fixtures/config.yaml')) throw new ConfigNotFoundException('File "fixtures/config.yaml" doesn\'t exist');

        $content = $this->generator->generate($yaml);

        $path = './fixtures/config.yaml';
        $dirname = dirname($path);

        if (!is_dir($dirname)) {
            mkdir($dirname, 0777, true);
        }

        $file = fopen($path, 'w');
        fwrite($file, Yaml::dump($content));

        $io->success('Succeded to create data for currents models into file ./fixtures/config.yaml');

        return Command::SUCCESS;
    }
}
