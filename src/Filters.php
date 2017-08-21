<?php

namespace PhalconFilters;

use Phalcon\Mvc\Model\Query\Builder;
use PhalconFilters\Support\Arr;
use PhalconFilters\Support\Str;
use ReflectionClass;

class Filters
{
    /**
     * @param Builder $builder
     * @param array $request
     * @return Builder
     */
    public static function apply(Builder $builder, array $request): Builder
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

            $relatedFilters = self::extractRelatedFilters($model, $request);

            if(empty($relatedFilters)) {
                continue;
            }

            $filterClass = self::getFilterClass($model);

            if (!class_exists($filterClass)) {
                continue;
            }

            $filters = self::removeIdSuffix($relatedFilters);

            /** @var QueryFilters $filter */
            $filter = new $filterClass($table["alias"], $filters);
            $filter->apply($builder);
        }

        return $builder;
    }

    private static function extractRelatedFilters(ReflectionClass $model, array $filters): array
    {
        $prefix = strtolower($model->getShortName()) . "_";

        $relatedFilters = Arr::preg_grep_keys("!^{$prefix}!", $filters);

        return self::removeModelPrefix($relatedFilters, $prefix);
    }

    private static function removeModelPrefix(array $filters, string $prefix): array
    {
        return Arr::map_keys(function ($key) use ($prefix) {
            return substr_replace($key, "", 0, strlen($prefix));
        }, $filters);
    }

    private static function removeIdSuffix(array $filters): array
    {
        return Arr::map_keys(function ($key) {
            $suffix = "_id";

            if (!Str::endsWith($key, $suffix)) {
                return $key;
            }

            return substr_replace($key, "", -strlen($suffix));
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
