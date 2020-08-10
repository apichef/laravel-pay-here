<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use ApiChef\PayHere\Subscription;
use ApiChef\PayHere\Tests\App\Product;
use ApiChef\PayHere\Tests\App\User;
use Faker\Generator as Faker;

$factory->define(Subscription::class, function (Faker $faker) {
    return [
        'amount' => 100,
        'currency' => 'LKR',
        'recurrence' => '1 Month',
        'duration' => 'Forever',
        'recurrence_status' => 0,
        'next_recurrence_date' => now()->addMonth(),
        'times_paid' => 1,
        'subscribable_type' => Product::class,
        'subscribable_id' => factory(Product::class),
        'payer_type' => User::class,
        'payer_id' => factory(User::class),
    ];
});

$factory->state(Subscription::class, 'active', [
    'recurrence_status' => 0,
]);
