<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use ApiChef\PayHere\Tests\App\Product;
use Faker\Generator as Faker;

$factory->define(Product::class, function (Faker $faker) {
    return [
        'name' => $faker->words(2, true),
        'price' => 100,
    ];
});
