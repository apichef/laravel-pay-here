<?php

namespace ApiChef\PayHere\Tests;

use ApiChef\PayHere\PayHereServiceProvider;
use ApiChef\PayHere\Support\Facades\PayHere;

class TestCase extends \Orchestra\Testbench\TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->loadMigrationsFrom(__DIR__ . '/database/migrations');
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');
        $this->withFactories(__DIR__ . '/database/factories');
    }
    protected function getPackageProviders($app)
    {
        return [
            PayHereServiceProvider::class,
        ];
    }

    protected function getPackageAliases($app)
    {
        return [
            'PayHere' => PayHere::class,
        ];
    }

    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('database.default', 'testbench');
        $app['config']->set('database.connections.testbench', [
            'driver'   => 'sqlite',
            'database' => ':memory:',
            'prefix'   => '',
        ]);
    }
}
