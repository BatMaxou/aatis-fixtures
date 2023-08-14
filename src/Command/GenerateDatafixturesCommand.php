<?php

namespace App\Command;

// use App\Service\Faker;
use App\Service\DataGenerator;
use Symfony\Component\Yaml\Yaml;
use App\Exception\ConfigNotFoundException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'app:datafixtures:generate',
)]
class GenerateDatafixturesCommand extends Command
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
            ->setDescription('Generate datafixtures for the currents models');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        /* FAKER TEST */

        // $arrayTest = ['a', 'b', 'c'];

        // var_dump(Faker::bool());
        // var_dump(Faker::chooseBothFrom($arrayTest));
        // var_dump(Faker::chooseKeyFrom($arrayTest));
        // var_dump(Faker::chooseValueFrom($arrayTest));
        // var_dump(Faker::company());
        // var_dump(Faker::firstName());
        // var_dump(Faker::float());
        // var_dump(Faker::hexa());
        // var_dump(Faker::int());
        // var_dump(Faker::ipv4());
        // var_dump(Faker::ipv6());
        // var_dump(Faker::lastName());
        // var_dump(Faker::oneOn(10));
        // var_dump(Faker::repeatPatern('firstName', ' / ', 3));
        // var_dump(Faker::stringInt());
        // var_dump(Faker::word());
        // var_dump(Faker::text(7));

        /* FAKER TEST */

        $io = new SymfonyStyle($input, $output);

        if (!$yaml = Yaml::parseFile('./datafixtures/config.yaml')) throw new ConfigNotFoundException('File "datafixtures/config.yaml" doesn\'t exist');

        $content = $this->generator->generate($yaml);

        $path = './datafixtures/config.yaml';
        $dirname = dirname($path);

        if (!is_dir($dirname)) {
            mkdir($dirname, 0777, true);
        }

        $file = fopen($path, 'w');
        fwrite($file, Yaml::dump($content));

        $io->success('Succeded to create data for currents models into file ./datafixtures/config.yaml');

        return Command::SUCCESS;
    }
}
