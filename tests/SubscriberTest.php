<?php

namespace ApiChef\PayHere\Tests;

use ApiChef\PayHere\Subscription;
use ApiChef\PayHere\Tests\App\Product;
use ApiChef\PayHere\Tests\App\User;

class SubscriberTest extends TestCase
{
    public function test_hasActiveSubscription()
    {
        /** @var User $user */
        $user = factory(User::class)->create();

        /** @var Product $product */
        $product = factory(Product::class)->create();

        factory(Subscription::class)->state('active')->create([
            'subscribable_id' => $product->id,
            'payer_id' => $user->id,
        ]);

        $this->assertTrue($user->hasActiveSubscription($product));

        /** @var User $anotherUser */
        $anotherUser = factory(User::class)->create();

        $this->assertFalse($anotherUser->hasActiveSubscription($product));
    }

    /**
     * @dataProvider subscriptionStatuses
     */
    public function test_hasActiveSubscription_returns_true_only_when_the_status_is_2($status, $isSubscriptionActive)
    {
        /** @var User $user */
        $user = factory(User::class)->create();

        /** @var Product $product */
        $product = factory(Product::class)->create();

        factory(Subscription::class)->create([
            'subscribable_id' => $product->id,
            'payer_id' => $user->id,
            'recurrence_status' => $status,
        ]);

        $this->assertEquals($isSubscriptionActive, $user->hasActiveSubscription($product));
    }

    public function subscriptionStatuses(): array
    {
        return [
            [0, true],
            [-1, false],
            [1, false],
        ];
    }
}
