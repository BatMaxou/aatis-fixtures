<?php

namespace Aatis\FixturesBundle\Command;

use Aatis\FixturesBundle\Service\Faker;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'aatis:faker:list',
)]
class FakerListCommand extends Command
{
    private array $infos;

    public function __construct()
    {
        parent::__construct();
        $reflect = new \ReflectionClass(Faker::class);
        $methods = $reflect->getMethods();
        foreach ($methods as $method) {
            preg_match('/\/\*\*\n     \* (.*)\n/', $method->getDocComment(), $match);
            $this->infos[] = [$method->getName(), $match[1]];
        }
    }

    protected function configure(): void
    {
        $this
            ->setDescription('List all the method of the faker');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $io->table(['name', 'help'], $this->infos);

        return Command::SUCCESS;
    }
}
