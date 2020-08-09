<?php

namespace ApiChef\PayHere\Tests\Http\Controllers;

use ApiChef\PayHere\Subscription;
use ApiChef\PayHere\Tests\TestCase;

class SubscriptionRedirectControllerTest extends TestCase
{
    public function test_success()
    {
        /** @var Subscription $subscription */
        $subscription = factory(Subscription::class)->create();
        $orderId = $subscription->getRouteKey();

        $this->get(route('pay-here.subscription-success', ['order_id' => $orderId]))
            ->assertRedirect(route(config('pay-here.routes_name.subscription_success'), $subscription));
    }

    public function test_canceled()
    {
        /** @var Subscription $subscription */
        $subscription = factory(Subscription::class)->create();
        $orderId = $subscription->getRouteKey();

        $this->get(route('pay-here.subscription-canceled', ['order_id' => $orderId]))
            ->assertRedirect(route(config('pay-here.routes_name.subscription_canceled'), $subscription));
    }
}
