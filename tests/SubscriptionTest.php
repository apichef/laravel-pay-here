<?php

declare(strict_types=1);

namespace ApiChef\PayHere\Tests;

use ApiChef\PayHere\PayHere;
use ApiChef\PayHere\Subscription;
use ApiChef\PayHere\Tests\App\Product;
use ApiChef\PayHere\Tests\App\User;

class SubscriptionTest extends TestCase
{
    public function test_make()
    {
        $tom = factory(User::class)->create();
        $theBook = factory(Product::class)->create();
        $price = 20;
        $currency = PayHere::CURRENCY_USD;

        $subscription = Subscription::make($theBook, $tom, '1 Month', 'Forever', $price, $currency);

        $this->assertEquals($price, $subscription->amount);
        $this->assertEquals($currency, $subscription->currency);
        $this->assertEquals(0, $subscription->status);
        $this->assertNull($subscription->validated);
        $this->assertEquals($tom->id, $subscription->payer->id);
        $this->assertEquals($theBook->id, $subscription->subscribable->id);
    }

    public function test_findByOrderId()
    {
        /** @var Subscription $subscription */
        $subscription = factory(Subscription::class)->create();

        $this->assertEquals($subscription->id, Subscription::findByOrderId($subscription->getRouteKey())->id);
    }
}
