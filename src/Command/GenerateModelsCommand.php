<?php

namespace Aatis\FixturesBundle\Command;

use Aatis\FixturesBundle\Service\ModelsGenerator;
use Symfony\Component\Yaml\Yaml;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'aatis:model:generate',
)]
class GenerateModelsCommand extends Command
{
    private ModelsGenerator $generator;

    public function __construct(ModelsGenerator $generator)
    {
        parent::__construct();
        $this->generator = $generator;
    }

    protected function configure(): void
    {
        $this
            ->setDescription('Generate the models for the currents entities');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $content = $this->generator->generate();
        $path = './fixtures/config.yaml';

        $dirname = dirname($path);
        if (!is_dir($dirname)) {
            mkdir($dirname, 0777, true);
        }

        $file = fopen($path, 'w');
        fwrite($file, Yaml::dump($content));

        $io->success('Succeded to create models into file ./fixtures/config.yaml');

        return Command::SUCCESS;
    }
}
