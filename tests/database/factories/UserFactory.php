<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use ApiChef\PayHere\Tests\App\User;
use Faker\Generator as Faker;

$factory->define(User::class, function (Faker $faker) {
    return [
        'name' => $faker->name,
    ];
});
