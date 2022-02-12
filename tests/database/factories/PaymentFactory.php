<?php

namespace Database\Factories;

use ApiChef\PayHere\Payment;
use ApiChef\PayHere\Tests\App\Product;
use ApiChef\PayHere\Tests\App\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class PaymentFactory extends Factory
{
    protected $model = Payment::class;

    public function definition(): array
    {
        return [
            'amount' => 100,
            'currency' => 'LKR',
            'payable_type' => Product::class,
            'payable_id' => Product::factory(),
            'payer_type' => User::class,
            'payer_id' => User::factory(),
        ];
    }

    public function success(): Factory
    {
        return $this->state(function () {
            return [
                'status' => 2,
            ];
        });
    }

    public function failed(): Factory
    {
        return $this->state(function () {
            return [
                'status' => -2,
            ];
        });
    }
}
