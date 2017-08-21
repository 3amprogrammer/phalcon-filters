<?php

namespace PhalconFilters\Support;

class Arr
{
    public static function preg_grep_keys($pattern, array $input, $flags = 0): array
    {
        $filteredKeys = preg_grep($pattern, array_keys($input), $flags);

        return array_intersect_key($input, array_flip($filteredKeys));
    }

    public static function map_keys($callback, array $array): array
    {
        $keys = array_map($callback, array_keys($array));

        return array_combine($keys, $array);
    }
}