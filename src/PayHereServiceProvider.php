<?php

namespace ApiChef\PayHere;

use ApiChef\PayHere\View\Components\CheckoutForm;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class PayHereServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->registerRoutes();

        $this->loadViewComponentsAs('pay-here', [
            CheckoutForm::class,
        ]);

        $this->loadViewsFrom(__DIR__.'/../resources/views', 'pay-here');

        $this->publishes([
            __DIR__.'/../config/pay-here.php' => config_path('pay-here.php'),
        ], 'config');

        $this->publishes([
            __DIR__.'/../database/migrations/payment/' => database_path('migrations'),
        ], 'migrations:payments');

        $this->publishes([
            __DIR__.'/../database/migrations/subscription/' => database_path('migrations'),
        ], 'migrations:subscriptions');
    }

    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__.'/../config/pay-here.php',
            'pay-here'
        );

        $this->app->singleton('pay-here', function (Application $app): PayHere {
            return $app->make(PayHere::class);
        });
    }

    private function registerRoutes(): void
    {
        Route::middleware(config('pay-here.middleware'))
            ->namespace('ApiChef\PayHere\Http\Controllers')
            ->prefix('pay-here')
            ->as('pay-here.')
            ->group(function () {
                Route::post('/notify', 'PaymentNotificationController')
                    ->name('notify');
            });
    }
}
