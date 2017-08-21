<?php

namespace PhalconFilters\Tests\Stubs\Filters;

use Phalcon\Mvc\Model\Query\Builder;
use PhalconFilters\QueryFilters;

class ProfileFilters extends QueryFilters
{
    protected function firstName(Builder $builder, $value)
    {
        $builder->andWhere($this->alias . ".first_name = :first_name:", ["first_name" => $value]);
    }
}