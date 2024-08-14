<?php

namespace ApiChef\PayHere\Tests;

use ApiChef\PayHere\Payment;
use ApiChef\PayHere\Tests\App\Product;
use ApiChef\PayHere\Tests\App\User;
use PHPUnit\Framework\Attributes\DataProvider;

class BuyerTest extends TestCase
{
    public function test_hasBought()
    {
        /** @var User $user */
        $user = User::factory()->create();

        /** @var Product $product */
        $product = Product::factory()->create();

        Payment::factory()->success()->create([
            'payable_id' => $product->id,
            'payer_id' => $user->id,
        ]);

        $this->assertTrue($user->hasBought($product));

        /** @var User $anotherUser */
        $anotherUser = User::factory()->create();

        $this->assertFalse($anotherUser->hasBought($product));
    }

    /**
     * @dataProvider paymentStatuses
     */
    public function test_hasBought_returns_true_only_when_the_status_is_2($status, $hasBought)
    {
        /** @var User $user */
        $user = User::factory()->create();

        /** @var Product $product */
        $product = Product::factory()->create();

        Payment::factory()->create([
            'payable_id' => $product->id,
            'payer_id' => $user->id,
            'status' => $status,
        ]);

        $this->assertEquals($hasBought, $user->hasBought($product));
    }

    public static function paymentStatuses(): array
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
