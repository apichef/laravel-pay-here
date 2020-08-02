<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use ApiChef\PayHere\Payment;
use ApiChef\PayHere\Tests\App\Product;
use ApiChef\PayHere\Tests\App\User;
use Faker\Generator as Faker;

$factory->define(Payment::class, function (Faker $faker) {
    return [
        'amount' => 100,
        'status' => 2,
        'currency' => 'LKR',
        'payable_type' => Product::class,
        'payable_id' => factory(Product::class),
        'payer_type' => User::class,
        'payer_id' => factory(User::class),
        'validated' => true,
    ];
});

$factory->state(Payment::class, 'pending', [
    'status' => 0,
    'validated' => null,
]);

$factory->state(Payment::class, 'canceled', [
    'status' => -1,
]);

$factory->state(Payment::class, 'failed', [
    'status' => -2,
]);

$factory->state(Payment::class, 'chargedback', [
    'status' => -3,
]);

$factory->state(Payment::class, 'invalid', [
    'status' => 0,
    'validated' => false,
]);
