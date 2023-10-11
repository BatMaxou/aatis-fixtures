<?php

namespace Aatis\FixturesBundle\Command;

use Aatis\FixturesBundle\Service\Faker;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'aatis:faker:test',
)]
class FakerTestCommand extends Command
{
    protected function configure(): void
    {
        $this
            ->setDescription('Test Command');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $bool = Faker::bool();

        // output :
        // $bool = true

        dump($bool);

        // default [min => 0, max => 1000]
        $int = Faker::int();

        $int2 = Faker::int([
            'min' => 0,
            'max' => 10,
        ]);

        // output :
        // $int = 401
        // $int2 = 5

        dump($int);
        dump($int2);

        // default [min => 0, max => 1000, round => 2]
        $float = Faker::float();

        $float2 = Faker::float([
            'min' => 0,
            'max' => 10,
            'round' => 3,
        ]);

        // output :
        // $float = 36.34
        // $float2 = 5.123

        dump($float);
        dump($float2);

        // default [min => 0, max => 65535]
        $hexa = Faker::hexa();

        $hexa2 = Faker::hexa([
            'min' => 0,
            'max' => 16,
        ]);

        // output :
        // $hexa = c4f7
        // $hexa2 = d

        dump($hexa);
        dump($hexa2);

        $oneOn = Faker::oneOn(10);

        // $oneOn2 = Faker::oneOn(-1);

        // output :
        // $oneOn = fales
        // $oneOn = Error

        dump($oneOn);
        // dump($oneOn2);

        // default 5
        $string = Faker::string();

        $string2 = Faker::string(2);

        // output :
        // $string = lccxn
        // $string2 = oa

        dump($string);
        dump($string2);

        // default [min => 0, max => 1000, length => 4]
        $stringInt = Faker::stringInt();

        $stringInt2 = Faker::stringInt([
            'min' => 0,
            'max' => 10,
            'length' => 2,
        ]);

        // output :
        // $stringInt = 0401
        // $stringInt2 = 05

        dump($stringInt);
        dump($stringInt2);

        $firstName = Faker::firstName();

        // output :
        // $firstName = Jean

        dump($firstName);

        $lastName = Faker::lastName();

        // output :
        // $lastName = Dupont

        dump($lastName);

        $company = Faker::company();

        // output :
        // $company = Apex 78 Innovations

        dump($company);

        $ipv4 = Faker::ipv4();

        // output :
        // $ipv4 = 89:34:236:199

        dump($ipv4);

        $ipv6 = Faker::ipv6();

        // output :
        // $ipv6 = b10d:e2ff:15b4:ad72:fc85:184d:ef28:a068

        dump($ipv6);

        $word = Faker::word();

        // output :
        // $word = labore

        dump($word);

        $text = Faker::text(8);
        $text2 = Faker::text(-1);

        // output :
        // $text = Duisex Occaecatut... Exid loremdese! Fugiatsunt loremproid, dolorea laboreex.
        // $text2 = ""

        dump($text);
        dump($text2);

        $array = ['red', 'green', 'blue'];

        $chooseValueFrom = Faker::chooseValueFrom($array);
        $chooseValueFrom2 = Faker::chooseValueFrom($array, 2);
        $chooseValueFrom3 = Faker::chooseValueFrom($array, 4);
        $chooseValueFrom4 = Faker::chooseValueFrom($array, -1);

        // output :
        // $chooseValueFrom = red
        // $chooseValueFrom2 = ['red', 'blue']
        // $chooseValueFrom3 = null
        // $chooseValueFrom4 = null

        dump($chooseValueFrom);
        dump($chooseValueFrom2);
        dump($chooseValueFrom3);
        dump($chooseValueFrom4);

        $array = [
            'France' => 'Paris',
            'Germany' => 'Berlin',
            'Italy' => 'Rome',
        ];

        $chooseKeyFrom = Faker::chooseKeyFrom($array);
        $chooseKeyFrom2 = Faker::chooseKeyFrom($array, 2);
        $chooseKeyFrom3 = Faker::chooseKeyFrom($array, 4);
        $chooseKeyFrom4 = Faker::chooseKeyFrom($array, -1);

        // output :
        // $chooseKeyFrom = France
        // $chooseKeyFrom2 = ['France', 'Italy']
        // $chooseKeyFrom3 = null
        // $chooseKeyFrom4 = null

        dump($chooseKeyFrom);
        dump($chooseKeyFrom2);
        dump($chooseKeyFrom3);
        dump($chooseKeyFrom4);

        $array = [
            'Ben' => 22,
            'Joe' => 25,
            'Max' => 23,
        ];

        $chooseBothFrom = Faker::chooseBothFrom($array);
        $chooseBothFrom2 = Faker::chooseBothFrom($array, 2);
        $chooseBothFrom3 = Faker::chooseBothFrom($array, 4);
        $chooseBothFrom4 = Faker::chooseBothFrom($array, -1);

        // output :
        // $chooseBothFrom = ['Ben' => 22]
        // $chooseBothFrom2 = ['Ben' => 22, 'Max' => 23]
        // $chooseBothFrom3 = null
        // $chooseBothFrom4 = null

        dump($chooseBothFrom);
        dump($chooseBothFrom2);
        dump($chooseBothFrom3);
        dump($chooseBothFrom4);

        $array = Faker::array();
        $array2 = Faker::array([
            'type' => 'int',
        ]);
        $array3 = Faker::array([
            'type' => 'int',
            'parameters' => [
                ['max' => 10],
            ],
        ]);
        $array4 = Faker::array([
            'type' => 'int',
            'parameters' => [
                ['max' => 10],
            ],
            'lenght' => 5,
        ]);

        $array5 = Faker::array([
            'type' => 'int',
            'lenght' => -1,
        ]);

        // output :
        // array = '[]'
        // array2 = '[292, 107, 988]'
        // array3 = '[4, 5, 3]'
        // array4 = '[5, 7, 2, 2, 1]'
        // array5 = '[]'

        dump($array);
        dump($array2);
        dump($array3);
        dump($array4);
        dump($array5);

        $json = Faker::json();
        $json2 = Faker::json(['jsonFirstName' => [
            'type' => 'firstName',
        ]]);
        $json3 = Faker::json(['jsonFullName' => [
            'type' => 'patern',
            'parameters' => [
                'Hello ! My name is :firstName: :lastName:.',
            ],
        ]]);

        // output :
        // $json = '{}'
        // $json2 = '{"jsonFirstName":"Jean"}'
        // $json3 = '{"jsonFullName":"Hello ! My name is
        //      B\u00e9atrice Charon."}'

        dump($json);
        dump($json2);
        dump($json3);

        return Command::SUCCESS;
    }
}
