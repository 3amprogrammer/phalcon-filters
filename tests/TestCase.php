<?php

namespace PhalconFilters\Tests;

use League\FactoryMuffin\FactoryMuffin;
use Phalcon\Config;
use Phalcon\Db\Adapter\Pdo\Sqlite as SqliteAdapter;
use Phalcon\Db\AdapterInterface as DbAdapter;
use Phalcon\Db\Column;
use Phalcon\Db\Index;
use Phalcon\Mvc\Model\Manager as ModelManager;
use Phalcon\Mvc\Model\Metadata\Memory as ModelMetadata;
use Phalcon\Test\UnitTestCase as PhalconTestCase;

abstract class TestCase extends PhalconTestCase
{
    /**
     * @var FactoryMuffin
     */
    protected static $factory;

    protected function setUp()
    {
        parent::setUp();

        $this->di->set('modelsManager', function () {
            return new ModelManager();
        });

        $this->di->set('modelsMetadata', function () {
            return new ModelMetadata();
        });
    }

    /**
     * Determine if two associative arrays are similar
     *
     * Both arrays must have the same indexes with identical values
     * without respect to key ordering
     *
     * @param array $a
     * @param array $b
     * @return bool
     */
    public static function assertArrayEquals($a, $b) {
        // if the indexes don't match, return immediately
        if (count(array_diff_assoc($a, $b))) {
            return false;
        }

        // we know that the indexes, but maybe not values, match.
        // compare the values between the two arrays
        foreach($a as $k => $v) {
            if ($v !== $b[$k]) {
                return false;
            }
        }

        // we have identical indexes, and no unequal values
        return true;
    }
}