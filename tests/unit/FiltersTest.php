<?php

namespace PhalconFilters\Tests\Unit;

use Phalcon\Mvc\Model\Query\Builder;
use PhalconFilters\Filters;
use PhalconFilters\Tests\Stubs\Address;
use PhalconFilters\Tests\Stubs\Profile;
use PhalconFilters\Tests\Stubs\User;
use PhalconFilters\Tests\TestCase;

class FiltersTest extends TestCase
{
    const SUCCESS = true;

    /**
     * @param Builder $builder
     * @param string $expected
     * @test
     * @dataProvider differentFromFormats
     */
    public function Apply_SingleTable_AppliesWhereClauses(Builder $builder, string $expected)
    {
        $request = [
            "user_email" => "john@doe.com",
            "some_filter" => "some_filter_value"
        ];

        Filters::apply($builder, $request);

        $where = $builder->getWhere();

        $this->assertNotNull($where);
        $this->assertContains($expected, $where);
        $this->assertNotContains("some_filter_value", $where);
    }

    /**
     * @param Builder $builder
     * @test
     * @dataProvider differentFromFormats
     */
    public function Apply_SingleTable_AppliesWhereClauses_WithRightBindValues(Builder $builder)
    {
        $request = [
            "user_email" => "john@doe.com",
            "some_filter" => "some_filter_value"
        ];

        Filters::apply($builder, $request);

        $bindParams = $builder->getQuery()->getBindParams();

        $this->assertNotNull($bindParams);
        $this->assertEquals([
            "email" => "john@doe.com"
        ], $bindParams);
        $this->assertNotContains("some_filter_value", $bindParams);
    }

    /**
     * @param Builder $builder
     * @param string $expected
     * @test
     * @dataProvider differentFromFormats
     */
    public function Apply_SingleJoinedTable_AppliesWhereClauses(Builder $builder, string $expected)
    {
        $request = [
            "user_email" => "john@doe.com",
            "profile_first_name" => "John",
            "some_filter" => "some_filter_value"
        ];

        Filters::apply($builder, $request);

        $where = $builder->getWhere();

        $this->assertNotNull($where);
        $this->assertContains($expected, $where);
        $this->assertNotContains("some_filter_value", $where);
    }

    /**
     * @param Builder $builder
     * @test
     * @dataProvider differentFromFormats
     */
    public function Apply_SingleJoinedTable_AppliesWhereClauses_WithRightBindValues(Builder $builder)
    {
        $request = [
            "user_email" => "john@doe.com",
            "profile_first_name" => "John",
            "some_filter" => "some_filter_value"
        ];

        Filters::apply($builder, $request);

        $bindParams = $builder->getQuery()->getBindParams();

        $this->assertNotNull($bindParams);
        $this->assertArrayEquals([
            "email" => "john@doe.com",
            "first_name" => "John",
        ], $bindParams);
        $this->assertNotContains("some_filter_value", $bindParams);
    }

    /**
     * @test
     */
    public function Apply_SingleJoinedTable_WithoutCorrespondingFilterClass_DoesNotThrowException()
    {
        $builder = (new Builder())
            ->columns("u.*")
            ->from(User::class)
            ->innerJoin(Address::class);

        $request = [
            "user_email" => "john@doe.com",
            "address_zip" => "00-000"
        ];

        Filters::apply($builder, $request);

        $this->assertTrue(self::SUCCESS);
    }

    public function differentFromFormats()
    {
        return [
            [(new Builder())
                ->columns("u.*")
                ->from(User::class)
                ->innerJoin(Profile::class),
                User::class . ".email = :email:"
            ],

            [(new Builder())
                ->columns("u.*")
                ->from(["u" => User::class])
                ->innerJoin(Profile::class, "p.user_id = u.user_id", "p"),
                "u.email = :email:"
            ],

            [(new Builder())
                ->columns("u.*")
                ->addFrom(User::class, "u")
                ->innerJoin(Profile::class, "p.user_id = u.user_id", "p"),
                "u.email = :email:"
            ]
        ];
    }
}
