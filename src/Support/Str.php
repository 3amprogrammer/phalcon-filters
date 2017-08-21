<?php

namespace PhalconFilters\Support;

class Str
{
    public static function pascalCase(string $str): string
    {
        return str_replace(" ", "", ucwords(str_replace("_", " ", $str)));
    }

    public static function camelCase(string $str): string
    {
        return lcfirst(self::pascalCase($str));
    }

    public static function startsWith($haystack, $needle)
    {
        $length = strlen($needle);
        return (substr($haystack, 0, $length) === $needle);
    }

    public static function endsWith($haystack, $needle)
    {
        $length = strlen($needle);

        if ($length == 0) {
            return true;
        }

        return (substr($haystack, -$length) === $needle);
    }

}