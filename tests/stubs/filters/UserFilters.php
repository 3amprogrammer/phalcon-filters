<?php

namespace PhalconFilters\Tests\Stubs\Filters;

use Phalcon\Mvc\Model\Query\Builder;
use PhalconFilters\QueryFilters;

class UserFilters extends QueryFilters
{
    protected function email(Builder $builder, $value)
    {
        $builder->andWhere($this->alias . ".email = :email:", ["email" => $value]);
    }
}