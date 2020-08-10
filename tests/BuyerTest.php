<?php

namespace ApiChef\PayHere\Tests;

use ApiChef\PayHere\Payment;
use ApiChef\PayHere\Tests\App\Product;
use ApiChef\PayHere\Tests\App\User;

class BuyerTest extends TestCase
{
    public function test_hasBought()
    {
        /** @var User $user */
        $user = factory(User::class)->create();

        /** @var Product $product */
        $product =  factory(Product::class)->create();

        factory(Payment::class)->state('success')->create([
            'payable_id' => $product->id,
            'payer_id' => $user->id,
        ]);

        $this->assertTrue($user->hasBought($product));

        /** @var User $anotherUser */
        $anotherUser = factory(User::class)->create();

        $this->assertFalse($anotherUser->hasBought($product));
    }

    /**
     * @dataProvider paymentStatuses
     */
    public function test_hasBought_returns_true_only_when_the_status_is_2($status, $hasBought)
    {
        /** @var User $user */
        $user = factory(User::class)->create();

        /** @var Product $product */
        $product =  factory(Product::class)->create();

        factory(Payment::class)->create([
            'payable_id' => $product->id,
            'payer_id' => $user->id,
            'status' => $status,
        ]);

        $this->assertEquals($hasBought, $user->hasBought($product));
    }

    public function paymentStatuses(): array
    {
        return [
            [2, true],
            [0, false],
            [-1, false],
            [-2, false],
            [-3, false],
        ];
    }
}
