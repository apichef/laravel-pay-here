<?php

namespace ApiChef\PayHere\Tests;

use ApiChef\Obfuscate\ObfuscateServiceProvider;
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
            ObfuscateServiceProvider::class,
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
        $app['config']->set('app.key', 'base64:hADMGSrHrQQ0ao6Kx4MQCZxUe/CQB3pI/dOdCfZb1aU=');
        $app['config']->set('app.debug', true);

        $app['config']->set('database.default', 'testbench');
        $app['config']->set('database.connections.testbench', [
            'driver'   => 'sqlite',
            'database' => ':memory:',
            'prefix'   => '',
        ]);

        $app['config']->set('pay-here.business_app_credentials.id', 'app_id');
        $app['config']->set('pay-here.business_app_credentials.secret', 'app_secret');
    }
}
