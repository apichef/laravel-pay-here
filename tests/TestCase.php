<?php

namespace ApiChef\PayHere\Tests;

use ApiChef\Obfuscate\ObfuscateServiceProvider;
use ApiChef\PayHere\PayHereServiceProvider;
use ApiChef\PayHere\Payment;
use ApiChef\PayHere\Subscription;
use ApiChef\PayHere\Support\Facades\PayHere;
use Illuminate\Support\Facades\Route;

class TestCase extends \Orchestra\Testbench\TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->loadMigrationsFrom(__DIR__.'/database/migrations');
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
        $this->withFactories(__DIR__.'/database/factories');
        $this->registerRoutes();
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

        $app['config']->set('pay-here.merchant_credentials.id', 'test_merchant_id');
        $app['config']->set('pay-here.merchant_credentials.secret', 'test_merchant_secret');

        $app['config']->set('pay-here.business_app_credentials.id', 'test_app_id');
        $app['config']->set('pay-here.business_app_credentials.secret', 'test_app_secret');
    }

    private function registerRoutes()
    {
        Route::get('payment-success/{payment}', function (Payment $payment) {
            return $payment->getRouteKey();
        })->name('payment_success');

        Route::get('payment-canceled/{payment}', function (Payment $payment) {
            return $payment->getRouteKey();
        })->name('payment_canceled');

        Route::get('subscription-success/{subscription}', function (Subscription $subscription) {
            return $subscription->getRouteKey();
        })->name('subscription_success');

        Route::get('subscription-canceled/{subscription}', function (Subscription $subscription) {
            return $subscription->getRouteKey();
        })->name('subscription_canceled');
    }
}
