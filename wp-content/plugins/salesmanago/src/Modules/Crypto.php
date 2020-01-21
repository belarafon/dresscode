<?php

namespace bhr\Modules;


class Crypto
{
    private static function atbash($string)
    {
        $atbash = Array(
            "a" => "Z", "g" => "T", "m" => "N", "s" => "H",
            "b" => "Y", "h" => "S", "n" => "M", "t" => "G",
            "c" => "X", "i" => "R", "o" => "L", "u" => "F",
            "d" => "W", "j" => "Q", "p" => "K", "v" => "E",
            "e" => "V", "k" => "P", "q" => "J", "w" => "D",
            "f" => "U", "l" => "O", "r" => "I", "x" => "C",
            "y" => "B", "z" => "A",
            "A" => "z", "G" => "t", "M" => "n", "S" => "h",
            "B" => "y", "H" => "s", "N" => "m", "T" => "g",
            "C" => "x", "I" => "r", "O" => "l", "U" => "f",
            "D" => "w", "J" => "q", "P" => "k", "V" => "e",
            "E" => "v", "K" => "p", "Q" => "j", "W" => "d",
            "F" => "u", "L" => "o", "R" => "i", "X" => "c",
            "Y" => "b", "Z" => "a",
        );

        return strtr($string, $atbash);
    }

    public static function encrypt($message, $compressed = true)
    {
        if ($compressed) {
            return self::atbash(strtr(base64_encode(str_rot13(gzdeflate(json_encode($message)))), '+/=', '._-'));
        }
        return self::atbash(strtr(base64_encode(json_encode($message)), '+/=', '._-'));
    }

    public static function decrypt($message, $compressed = true)
    {
        if ($compressed) {
            return json_decode(gzinflate(str_rot13(base64_decode(strtr(self::atbash($message), '._-', '+/=')))), true);
        }
        return json_decode(base64_decode(strtr(self::atbash($message), '._-', '+/=')), true);
    }
}