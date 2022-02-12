<?php

namespace Database\Factories;

use ApiChef\PayHere\Subscription;
use ApiChef\PayHere\Tests\App\Product;
use ApiChef\PayHere\Tests\App\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class SubscriptionFactory extends Factory
{
    protected $model = Subscription::class;

    public function definition(): array
    {
        return [
            'amount' => 100,
            'currency' => 'LKR',
            'recurrence' => '1 Month',
            'duration' => 'Forever',
            'recurrence_status' => 0,
            'next_recurrence_date' => now()->addMonth(),
            'times_paid' => 1,
            'subscribable_type' => Product::class,
            'subscribable_id' => Product::factory(),
            'payer_type' => User::class,
            'payer_id' => User::factory(),
        ];
    }

    public function active(): Factory
    {
        return $this->state(function () {
            return [
                'recurrence_status' => 0,
            ];
        });
    }
}
