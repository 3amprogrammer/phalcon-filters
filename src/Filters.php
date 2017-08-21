<?php

namespace PhalconFilters;

use Phalcon\Mvc\Model\Query\Builder;
use PhalconFilters\Support\Arr;
use ReflectionClass;

class Filters
{
    /**
     * @param Builder $builder
     * @param array $filters
     * @return Builder
     */
    public static function apply(Builder $builder, array $filters): Builder
    {
        $from = (array)$builder->getFrom();
        $joins = (array)$builder->getJoins();

        $tables = array_map(function ($table) {
            reset($table);

            $key = key($table);

            if (is_string($key)) {
                $alias = $key;
                $model = $table[$key];
            } else if (isset($table[2]) && !is_null($table[2])) {
                $alias = $table[2];
                $model = $table[0];
            } else {
                $alias = $table[0];
                $model = $table[0];
            }

            return [
                "model" => $model,
                "alias" => $alias
            ];
        }, array_merge($joins, [$from]));


        foreach ($tables as $table) {

            $model = new ReflectionClass($table["model"]);
            $filterClass = self::getFilterClass($model);

            $relatedFilters = self::extractRelatedFilters($model, $filters);

            /** @var QueryFilters $filter */
            $filter = new $filterClass($table["alias"], $relatedFilters);
            $filter->apply($builder);
        }

        return $builder;
    }

    private static function extractRelatedFilters(ReflectionClass $model, array $filters): array
    {
        $prefix = strtolower($model->getShortName()) . "_";

        $relatedFilters = Arr::preg_grep_keys("!^{$prefix}!", $filters);

        return self::removePrefix($relatedFilters, $prefix);
    }

    private static function removePrefix(array $filters, string $prefix): array
    {
        return Arr::map_keys(function ($key) use ($prefix) {
            return substr_replace($key, "", 0, strlen($prefix));
        }, $filters);
    }

    /**
     * @param $model
     * @return string
     */
    private static function getFilterClass(ReflectionClass $model): string
    {
        return $model->getNamespaceName() . "\\Filters\\" . $model->getShortName() . "Filters";
    }
}