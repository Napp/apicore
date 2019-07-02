<?php

namespace Napp\Core\Api\Tests;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Napp\Core\Api\Requests\Provider\RequestServiceProvider;
use Napp\Core\Api\Router\Provider\RouterServiceProvider;

class TestCase extends \Orchestra\Testbench\TestCase
{
    public static $migrated = false;

    /**
     * Setup the test environment.
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->migrateTables();
    }

    public function setUpTestDatabases()
    {
        if (false === static::$migrated) {
            $this->dropAllTables();

            $this->migrateTables();

            static::$migrated = true;
        }

        //$this->beginDatabaseTransaction();
    }

    /**
     * Define environment setup.
     *
     * @param \Illuminate\Foundation\Application $app
     *
     * @return void
     */
    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('cache.default', 'array');

        // mysql
        /*$app['config']->set('database.default', 'mysql');
        $app['config']->set('database.connections.mysql', [
            'driver'    => 'mysql',
            'host'      => 'localhost',
            'database'  => 'apicore',
            'username'  => 'username',
            'password'  => 'password',
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix' => '',
            'strict' => true,
            'engine' => null,
        ]);*/

        // sqlite
        $app['config']->set('database.connections.testing', [
            'driver'   => 'sqlite',
            'database' => ':memory:',
            'prefix'   => '',
        ]);
        $app['config']->set('database.default', 'testing');
    }

    /**
     * Loading package service provider.
     *
     * @param \Illuminate\Foundation\Application $app
     *
     * @return array
     */
    protected function getPackageProviders($app)
    {
        return [
            RequestServiceProvider::class,
            RouterServiceProvider::class,
        ];
    }

    public function migrateTables()
    {
        if (!Schema::hasTable('categories')) {
            Schema::create('categories', function (Blueprint $table) {
                $table->increments('id');
                $table->string('title');
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('products')) {
            Schema::create('products', function (Blueprint $table) {
                $table->increments('id');
                $table->string('name');
                $table->float('price');
                $table->integer('category_id')->unsigned();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('variants')) {
            Schema::create('variants', function (Blueprint $table) {
                $table->increments('id');
                $table->string('name');
                $table->float('sku_id');
                $table->integer('product_id')->unsigned();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('posts')) {
            Schema::create('posts', function (Blueprint $table) {
                $table->increments('id');
                $table->string('title');
                $table->text('desc');
                $table->json('tags');
                $table->text('other_tags')->nullable();
                $table->integer('owner')->nullable();
                $table->uuid('uuid')->nullable();
                $table->timestamps();
            });
        }
    }

    /**
     * Drop all tables to start the test with fresh data.
     */
    public function dropAllTables()
    {
        Schema::disableForeignKeyConstraints();
        collect(DB::select('SHOW TABLES'))
            ->map(function (\stdClass $tableProperties) {
                return get_object_vars($tableProperties)[key($tableProperties)];
            })
            ->each(function (string $tableName) {
                Schema::drop($tableName);
            });
        Schema::enableForeignKeyConstraints();
    }
}
