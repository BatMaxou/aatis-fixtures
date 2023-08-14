<?php

namespace App\Service;

use App\Service\FakerProvider;
use App\Exception\Faker\IntRangeException;
use App\Exception\Faker\RoundException;

class Faker
{
    /**
     * Generate a string of a patern repeated n times separate by the given separator.
     * 
     * @param string $functionName The name of the patern you want to be repeated.
     * @param string $separator A string that will be place between each patern.
     * @param int $nbPaternWanted The number of time you want the patern to be repeated.
     * @param array $parameters Inform the necessary parameters to execute fonctionName.
     * 
     * @return string
     * 
     * @throws IntRangeException
     */
    public static function repeatPatern(string $functionName, string $separator, int $nbPaternWanted, array $parameters = []): string
    {
        if ($nbPaternWanted <= 0) throw new IntRangeException('The number of patern wanted can not be negative or null.');

        $string = '';
        for ($i = 1; $i <= $nbPaternWanted; $i++) {
            $string .= strval(call_user_func('self::' . $functionName, ...$parameters));

            if ($i !== $nbPaternWanted) {
                $string .= $separator;
            }
        }

        return $string;
    }

    /**
     * Generate a random number.
     * 
     * @param array $options You can precise the range wanted with a maximum and a minimum (default [0:1000]).
     * 
     * @return int
     * 
     * @throws IntRangeException
     */
    public static function int(array $options = [
        'min' => 0,
        'max' => 1000
    ]): ?int
    {
        $min = isset($options['min']) ? $options['min'] : 0;
        $max = isset($options['max']) ? $options['max'] : 1000;

        return ($min > $max) ? throw new IntRangeException('The minimum must be greater than the maximum.') : random_int($min, $max);
    }

    /**
     * Generate a random boolean.
     * 
     * @return bool
     */
    public static function bool(): bool
    {
        return (self::int(['max' => 1])) ? true : false;
    }

    /**
     * Generate a random float.
     * 
     * @param array $options You can precise the range wanted with a maximum and a minimum and a round for the number of digit wanted after the point (default [0:1000] round by 2).
     * 
     * @return float
     * 
     * @throws RoundException
     * @throws IntRangeException
     */
    public static function float(array $options = [
        'min' => 0,
        'max' => 1000,
        'round' => 2
    ]): float
    {
        $min = isset($options['min']) ? $options['min'] : 0;
        $max = isset($options['max']) ? $options['max'] : 1000;
        $round = isset($options['round']) ? $options['round'] : 2;

        if ($round < 0) throw new RoundException('The round option can not be lower than 0.');

        return ($min > $max) ? throw new IntRangeException('The minimum must be greater than the maximum.') : round($min + rand() / getrandmax() * ($max - $min), $round);
    }

    /**
     * Generate a random hexadecimal number.
     * 
     * @param array $options You can precise the range wanted with a maximum and a minimum (default [0:65535]).
     * 
     * @return string
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
     * @param int $odds The number by which 1 is divided.
     * 
     * @return bool
     */
    public static function oneOn(int $odds): bool
    {
        if ($odds < 0) {
            throw new IntRangeException('The number of odds can not be negative');
        } else if ($odds === 0) {
            return false;
        }

        return (self::int(['max' => ($odds - 1)]) === self::int(['max' => ($odds - 1)])) ? true : false;
    }

    /**
     * Return a random string of n characters base on the alphabet.
     * 
     * @param int $lenght You can precise the lenght of the string (default 5).
     * 
     * @return string
     */
    public static function string(int $lenght = 5): string
    {
        return self::repeatPatern('chooseValueFrom', '', $lenght, [FakerProvider::ALPHABET]);
    }

    /**
     * Generate a random number into a string.
     * 
     * @param array $options You can precise the range wanted with a maximum and a minimum (default [0:1000]) and the number of digit that you want (default 4).
     * 
     * @return string
     * 
     * @throws IntRangeException
     */
    public static function stringInt(array $options = [
        'min' => 0,
        'max' => 1000,
        'lenght' => 4
    ])
    {
        $min = isset($options['min']) ? $options['min'] : 0;
        $max = isset($options['max']) ? $options['max'] : 1000;
        $lenght = isset($options['lenght']) ? $options['lenght'] : 4;

        if (strlen(strval($max)) > $lenght) throw new IntRangeException('The maximum can not have more digit than the given lenght.');

        $string = strval(self::int(['min' => $min, 'max' => $max]));
        while (strlen($string) < $lenght) $string = '0' . $string;

        return $string;
    }

    /**
     * Choose a random value from a given array.
     * 
     * @param array $array The array where you want to choose the value.
     * @param int $nbElementsWanted You can precise the number of value that you want (default 1).
     * 
     * @return mixed If you want severals values this method return an array, otherwise it return a value.
     */
    public static function chooseValueFrom(array $array, $nbElementsWanted = 1): mixed
    {
        if ($nbElementsWanted < 1 || $nbElementsWanted > count($array)) {
            return null;
        } else if ($nbElementsWanted === 1) {
            $keys = array_keys($array);
            $key = $keys[self::int(['max' => count($keys) - 1])];

            return $array[$key];
        }

        $chosen = [];
        for ($i = 0; $i < $nbElementsWanted; $i++) {
            $key = self::chooseKeyFrom($array);
            $chosen[] = $array[$key];
            unset($array[$key]);
        }

        return $chosen;
    }

    /**
     * Choose a random key from a given array.
     * 
     * @param array $array The array where you want to choose the key.
     * @param int $nbElementsWanted You can precise the number of key that you want (default 1).
     * 
     * @return mixed If you want severals keys this method return an array, otherwise it return a key.
     */
    public static function chooseKeyFrom(array $array, $nbElementsWanted = 1): mixed
    {
        if ($nbElementsWanted < 1 || $nbElementsWanted > count($array)) {
            return null;
        } else if ($nbElementsWanted === 1) {
            return self::chooseValueFrom(array_keys($array));
        }

        $chosen = [];
        for ($i = 0; $i < $nbElementsWanted; $i++) {
            $key = self::chooseKeyFrom($array);
            $chosen[] = self::chooseKeyFrom($array);
            unset($array[$key]);
        }

        return $chosen;
    }

    /**
     * Choose a random element (key => value) from a given array.
     * 
     * @param array $array The array where you want to choose the element.
     * @param int $nbElementsWanted You can precise the number of element that you want (default 1).
     * 
     * @return mixed If you want severals elements this method return an array, otherwise it return an element.
     */
    public static function chooseBothFrom(array $array, $nbElementsWanted = 1): ?array
    {
        if ($nbElementsWanted < 1 || $nbElementsWanted > count($array)) {
            return null;
        } else if ($nbElementsWanted === 1) {
            $key = self::chooseKeyFrom($array);
            return [$key => $array[$key]];
        }

        $chosen = [];
        for ($i = 0; $i < $nbElementsWanted; $i++) {
            $key = self::chooseKeyFrom($array);
            $chosen[] = [$key => $array[$key]];
            unset($array[$key]);
        }

        return $chosen;
    }

    /**
     * Return a random first name.
     * 
     * @return string
     */
    public static function firstName(): string
    {
        return self::chooseValueFrom(FakerProvider::FIRST_NAMES);
    }

    /**
     * Return a random last name.
     * 
     * @return string
     */
    public static function lastName(): string
    {
        return self::chooseValueFrom(FakerProvider::LAST_NAMES);
    }

    /**
     * Return a random company name.
     * 
     * @return string
     */
    public static function company(): string
    {
        $body = self::bool() ? self::chooseValueFrom(['lastName', 'int']) : null;
        $body = isset($body) ? ' ' . self::$body() . ' ' : self::chooseValueFrom(['&', ' and ', ' ']);
        $extension = (self::oneOn(10)) ? '™' : '';

        return self::chooseValueFrom(FakerProvider::COMPANY_PREFIXES) . $body . self::chooseValueFrom(FakerProvider::COMPANY_SUFFIXES) . $extension;
    }

    /**
     * Return a random ipv4 adress.
     * 
     * @return string
     */
    public static function ipv4(): string
    {
        return self::repeatPatern('int', ':', 4, [['max' => 255, 'lenght' => 3]]);
    }

    /**
     * Return a random ipv6 adress.
     * 
     * @return string
     */
    public static function ipv6(): string
    {
        return self::repeatPatern('hexa', ':', 8, [['max' => 65535]]);
    }

    /**
     * Return a random fake word.
     * 
     * @return string
     */
    public static function word(): string
    {
        return self::chooseValueFrom(FakerProvider::WORDS_LOREM);
    }

    /**
     * Return a random text of fake word.
     * 
     * @param int $nbWords The number of words wanted.
     * @return string
     */
    public static function text(int $nbWords): string
    {
        $reset = true;
        $text = ucfirst(self::word());
        for ($i = 2; $i <= $nbWords; $i++) {
            if ($reset) {
                $reset = false;
                $text .= ' ' . ucfirst(self::word());
            } else {
                $text .= ' ' . self::word();
            }

            if ($i === $nbWords) {
                $text .= '.';
            } else if (self::oneOn(5)) {
                $text .= self::chooseValueFrom(['.', '!', '?', '...']);
                $reset = true;
            } else if (self::oneOn(3)) {
                $text .= ',';
            }
        }

        return $text;
    }
}
