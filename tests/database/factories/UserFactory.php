<?php

namespace Database\Factories;

use ApiChef\PayHere\Tests\App\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class UserFactory extends Factory
{
    protected $model = User::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->name,
        ];
    }
}
