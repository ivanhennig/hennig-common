<?php

namespace Hennig\Common;

use Illuminate\Container\Container;
use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Database\Connection;
use Illuminate\Events\Dispatcher;

class Database
{
    /**
     * @var Connection
     */
    static $connection = null;
    static public function init()
    {
        $capsule = new Capsule;

        $capsule->getDatabaseManager()->extend('mongodb', function($config, $name)
        {
            $config['name'] = $name;

            return new Jenssegers\Mongodb\Connection($config);
        });

        $capsule->addConnection([
            'driver'    => Config::get("db_driver"),
            'host'      => Config::get("db_host"),
            'database'  => Config::get("db_dbname"),
            'username'  => Config::get("db_user"),
            'password'  => Config::get("db_pass"),
            'charset'   => 'utf8',
            'collation' => 'utf8_unicode_ci',
            'prefix'    => '',
        ]);

        $capsule->setEventDispatcher(new Dispatcher(new Container));
        // Make this Capsule instance available globally via static methods
        $capsule->setAsGlobal();
        // Setup the Eloquent ORM
        $capsule->bootEloquent();

        self::$connection = $capsule->getConnection();
    }

    /**
     * @return Connection
     */
    static public function connection()
    {
        return self::$connection;
    }

    /**
     * @param $v
     * @return string
     */
    static public function quot($v)
    {
        return self::$connection->getPdo()->quote($v);
    }
}