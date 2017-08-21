<?php

namespace PhalconFilters;

use Phalcon\Mvc\Model\Query\Builder;
use PhalconFilters\Support\Str;

abstract class QueryFilters
{
    /**
     * @var string
     */
    protected $alias;

    /**
     * @var array
     */
    private $filters;

    /**
     * Filters constructor.
     * @param string $alias
     * @param array $filters
     */
    public function __construct($alias, array $filters)
    {
        $this->alias = $alias;
        $this->filters = $filters;
    }

    /**
     * @param Builder $builder
     */
    public function apply(Builder $builder)
    {
        foreach ($this->filters as $field => $value) {
            $method = $this->createMethodName($field);

            if (method_exists($this, $method)) {
                $this->{$method}($builder, $value);
            }
        }
    }

    /**
     * @param string $field
     * @return string
     */
    private function createMethodName(string $field): string
    {
        return Str::camelCase($field);
    }
}