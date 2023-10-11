<?php

namespace Aatis\FixturesBundle\Command;

use Aatis\FixturesBundle\Exception\FileRightsException;
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

        $io->success('Succeded to create models into file ./config/fixtures/config.yaml');

        return Command::SUCCESS;
    }
}
