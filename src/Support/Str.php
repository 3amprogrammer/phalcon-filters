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
}