<?php

namespace Aatis\FixturesBundle\Service;

use Aatis\FixturesBundle\Exception\Faker\IntRangeException;
use Aatis\FixturesBundle\Exception\Faker\RoundException;

class Faker
{
    /**
     * Generate a string base on a patern. You can give faker methods between :: (:method:) and precise there parameters into.
     *
     * @param string $patern patern of the returned string wanted
     * @param array<string|int, array<array<string|int, string|int>|string|int>> $parameters array including the parameters of the methods given into the patern
     * @param bool $isAssociative inform if you want to use an associative array for var $parameters (['method' => [parameters]])
     */
    public static function patern(string $patern, array $parameters = [], bool $isAssociative = false): string
    {
        preg_match_all('/:([a-zA-Z0-9]*):/', $patern, $matches);

        for ($i = 0; $i < count($matches[0]); ++$i) {
            $method = $matches[1][$i];
            /**
             * @var callable $callable
             */
            $callable = self::class.'::'.$method;
            $result = '';
            $isSet = false;

            if (!empty($parameters)) {
                if ($isAssociative) {
                    if (in_array($method, array_keys($parameters))) {
                        $result = call_user_func($callable, $parameters[$method]);
                        $isSet = true;
                    }
                } else {
                    if (isset($parameters[$i])) {
                        $result = call_user_func($callable, ...$parameters[$i]);
                        $isSet = true;
                    }
                }
            }

            if (!$isSet) {
                $result = call_user_func($callable);
            }

            $string = '';
            $string = self::addElementToString($string, $result);
            $patern = str_replace($matches[0][$i], $string, $patern);
        }

        return $patern;
    }

    /**
     * Generate a string of a patern repeated n times separate by the given separator.
     *
     * @param string $functionName the name of the patern you want to be repeated
     * @param string $separator a string that will be place between each patern
     * @param int $nbPaternWanted the number of time you want the patern to be repeated
     * @param array<string|int, array<array<string|int, string|int>|string|int>> $parameters inform the necessary parameters to execute fonctionName
     *
     * @throws IntRangeException
     */
    public static function repeatPatern(string $functionName, string $separator, int $nbPaternWanted, array $parameters = []): string
    {
        if ($nbPaternWanted <= 0) {
            throw new IntRangeException('The number of patern wanted can not be negative or null.');
        }

        $string = '';
        for ($i = 1; $i <= $nbPaternWanted; ++$i) {
            /**
             * @var callable $callable
             */
            $callable = self::class.'::'.$functionName;
            $paternElement = call_user_func($callable, ...$parameters);

            $string = self::addElementToString($string, $paternElement);

            if ($i !== $nbPaternWanted) {
                $string .= $separator;
            }
        }

        return $string;
    }

    private static function addElementToString(string &$string, mixed $element): string
    {
        if (is_string($element)) {
            $string .= $element;
        } elseif (is_int($element) || is_float($element)) {
            $string .= (string) $element;
        } elseif (is_array($element)) {
            for ($i = 0; $i < count($element); ++$i) {
                if (0 !== $i) {
                    $string .= ', ';
                }

                self::addElementToString($string, $element[$i]);
            }
        } else {
            $string .= ":this patern don\'t return string|int|array<in|string):";
        }

        return $string;
    }

    /**
     * Generate a random number.
     *
     * @param array{
     *      min?: int,
     *      max?: int
     * } $options you can precise the range wanted with a maximum and a minimum (default [0:1000])
     *
     * @throws IntRangeException
     */
    public static function int(array $options = [
        'min' => 0,
        'max' => 1000,
    ]): int
    {
        $min = $options['min'] ?? 0;
        $max = $options['max'] ?? 1000;

        return ($min > $max) ? throw new IntRangeException('The minimum must be greater than the maximum.') : random_int($min, $max);
    }

    /**
     * Generate a random boolean.
     */
    public static function bool(): bool
    {
        return (self::int(['max' => 1])) ? true : false;
    }

    /**
     * Generate a random float.
     *
     * @param array{
     *      min?: int,
     *      max?: int,
     *      round?: int
     * } $options you can precise the range wanted with a maximum and a minimum and a round for the number of digit wanted after the point (default [0:1000] round by 2)
     *
     * @throws RoundException
     * @throws IntRangeException
     */
    public static function float(array $options = [
        'min' => 0,
        'max' => 1000,
        'round' => 2,
    ]): float
    {
        $min = $options['min'] ?? 0;
        $max = $options['max'] ?? 1000;
        $round = $options['round'] ?? 2;

        if ($round < 0) {
            throw new RoundException('The round option can not be lower than 0.');
        }

        return ($min > $max) ? throw new IntRangeException('The minimum must be greater than the maximum.') : round($min + rand() / getrandmax() * ($max - $min), $round);
    }

    /**
     * Generate a random hexadecimal number.
     *
     * @param array{
     *      min?: int,
     *      max?: int
     * } $options you can precise the range wanted with a maximum and a minimum (default [0:65535])
     */
    public static function hexa(array $options = [
        'min' => 0,
        'max' => 65535,
    ]): string
    {
        return dechex(self::int($options));
    }

    /**
     * Return true with the probability of 1/$odds, otherwise return false.
     *
     * @param int $odds the number by which 1 is divided
     */
    public static function oneOn(int $odds): bool
    {
        if ($odds < 0) {
            throw new IntRangeException('The number of odds can not be negative');
        } elseif (0 === $odds) {
            return false;
        }

        return (self::int(['max' => ($odds - 1)]) === self::int(['max' => ($odds - 1)])) ? true : false;
    }

    /**
     * Return a random string of n characters base on the alphabet.
     *
     * @param int $length you can precise the length of the string (default 5)
     */
    public static function string(int $length = 5): string
    {
        return self::repeatPatern('chooseValueFrom', '', $length, [FakerProvider::ALPHABET]);
    }

    /**
     * Generate a random number into a string.
     *
     * @param array{
     *      min?: int,
     *      max?: int,
     *      length?: int
     * } $options you can precise the range wanted with a maximum and a minimum (default [0:1000]) and the number of digit that you want (default 4)
     *
     * @return string
     *
     * @throws IntRangeException
     */
    public static function stringInt(array $options = [
        'min' => 0,
        'max' => 1000,
        'length' => 4,
    ])
    {
        $min = $options['min'] ?? 0;
        $max = $options['max'] ?? 1000;
        $length = $options['length'] ?? 4;

        if (strlen(strval($max)) > $length) {
            throw new IntRangeException('The maximum can not have more digit than the given length.');
        }

        $string = strval(self::int(['min' => $min, 'max' => $max]));
        while (strlen($string) < $length) {
            $string = '0'.$string;
        }

        return $string;
    }

    /**
     * Choose a random value from a given array.
     *
     * @param mixed[] $array the array where you want to choose the value
     * @param int $nbElementsWanted you can precise the number of value that you want (default 1)
     *
     * @return mixed if you want severals values this method return an array, otherwise it return a value
     */
    public static function chooseValueFrom(array $array, $nbElementsWanted = 1): mixed
    {
        if ($nbElementsWanted < 1 || $nbElementsWanted > count($array)) {
            return null;
        } elseif (1 === $nbElementsWanted) {
            $keys = array_keys($array);
            $key = $keys[self::int(['max' => count($keys) - 1])];

            return $array[$key];
        }

        $chosen = [];
        for ($i = 0; $i < $nbElementsWanted; ++$i) {
            $key = self::chooseKeyFrom($array);
            $chosen[] = $array[$key];
            unset($array[$key]);
        }

        return $chosen;
    }

    /**
     * Choose a random key from a given array.
     *
     * @param mixed[] $array the array where you want to choose the key
     * @param int $nbElementsWanted you can precise the number of key that you want (default 1)
     *
     * @return mixed if you want severals keys this method return an array, otherwise it return a key
     */
    public static function chooseKeyFrom(array $array, $nbElementsWanted = 1): mixed
    {
        if ($nbElementsWanted < 1 || $nbElementsWanted > count($array)) {
            return null;
        } elseif (1 === $nbElementsWanted) {
            return self::chooseValueFrom(array_keys($array));
        }

        $chosen = [];
        for ($i = 0; $i < $nbElementsWanted; ++$i) {
            $key = self::chooseKeyFrom($array);
            $chosen[] = self::chooseKeyFrom($array);
            unset($array[$key]);
        }

        return $chosen;
    }

    /**
     * Choose a random element (key => value) from a given array.
     *
     * @param mixed[] $array the array where you want to choose the element
     * @param int $nbElementsWanted you can precise the number of element that you want (default 1)
     *
     * @return array<int|string, mixed> if you want severals elements this method return an array, otherwise it return an element
     */
    public static function chooseBothFrom(array $array, $nbElementsWanted = 1): ?array
    {
        if ($nbElementsWanted < 1 || $nbElementsWanted > count($array)) {
            return null;
        } elseif (1 === $nbElementsWanted) {
            $key = self::chooseKeyFrom($array);

            return [$key => $array[$key]];
        }

        $chosen = [];
        for ($i = 0; $i < $nbElementsWanted; ++$i) {
            $key = self::chooseKeyFrom($array);
            $chosen[] = [$key => $array[$key]];
            unset($array[$key]);
        }

        return $chosen;
    }

    /**
     * Return a random first name.
     */
    public static function firstName(): string
    {
        /**
         * @var string $firstName
         */
        $firstName = self::chooseValueFrom(FakerProvider::FIRST_NAMES);

        return $firstName;
    }

    /**
     * Return a random last name.
     */
    public static function lastName(bool $toUpper = false): string
    {
        /**
         * @var string $lastName
         */
        $lastName = self::chooseValueFrom(FakerProvider::LAST_NAMES);

        if (!$toUpper) {
            return ucfirst(strtolower($lastName));
        }

        return $lastName;
    }

    /**
     * Return a random company name.
     */
    public static function company(): string
    {
        $body = self::bool() ? self::chooseValueFrom(['lastName', 'int']) : null;
        $body = isset($body) ? ' '.self::$body().' ' : self::chooseValueFrom(['&', ' and ', ' ']);
        $extension = (self::oneOn(10)) ? '™' : '';

        return self::chooseValueFrom(FakerProvider::COMPANY_PREFIXES).$body.self::chooseValueFrom(FakerProvider::COMPANY_SUFFIXES).$extension;
    }

    /**
     * Return a random ipv4 adress.
     */
    public static function ipv4(): string
    {
        return self::repeatPatern('int', ':', 4, [['max' => 255, 'length' => 3]]);
    }

    /**
     * Return a random ipv6 adress.
     */
    public static function ipv6(): string
    {
        return self::repeatPatern('hexa', ':', 8, [['max' => 65535]]);
    }

    /**
     * Return a random fake word.
     */
    public static function word(): string
    {
        /**
         * @var string $word
         */
        $word = self::chooseValueFrom(FakerProvider::WORDS_LOREM);

        return $word;
    }

    /**
     * Return a random text of fake word.
     *
     * @param int $nbWords the number of words wanted
     */
    public static function text(int $nbWords): string
    {
        if ($nbWords < 1) {
            return '';
        }

        $reset = true;
        $text = ucfirst(self::word());

        for ($i = 2; $i <= $nbWords; ++$i) {
            if ($reset) {
                $reset = false;
                $text .= ' '.ucfirst(self::word());
            } else {
                $text .= ' '.self::word();
            }

            if ($i === $nbWords) {
                $text .= '.';
            } elseif (self::oneOn(5)) {
                $text .= self::chooseValueFrom(['.', '!', '?', '...']);
                $reset = true;
            } elseif (self::oneOn(3)) {
                $text .= ',';
            }
        }

        return $text;
    }

    /**
     * Return a json which can be personalize.
     *
     * @param array<array{
     *      type: string,
     *      parameters?: mixed[]
     * }> $parameters array including the type of data you want for each key of your json
     */
    public static function json(array $parameters = []): string
    {
        if (empty($parameters)) {
            return '{}';
        }

        $returned = [];

        foreach ($parameters as $key => $infos) {
            $method = $infos['type'];
            $returned[$key] = isset($infos['parameters']) ? self::$method(...$infos['parameters']) : self::$method();
        }

        $json = json_encode($returned);

        if (!$json) {
            return '{}';
        }

        return $json;
    }

    /**
     * Return a string with an array into, which can be personalize.
     *
     * @param array{
     *      type?: string,
     *      parameters?: mixed[],
     *      lenght?: int
     * } $parameters array including the type of data you want for each key of your array
     */
    public static function array(array $parameters = []): string
    {
        if (empty($parameters)) {
            return '[]';
        }

        $lenght = $parameters['lenght'] ?? 3;

        if ($lenght < 0) {
            return '[]';
        }

        $returned = [];

        for ($i = 0; $i < $lenght; ++$i) {
            $method = $parameters['type'];
            $returned[] = isset($parameters['parameters']) ? self::$method(...$parameters['parameters']) : self::$method();
        }

        return '['.join(', ', $returned).']';
    }
}
