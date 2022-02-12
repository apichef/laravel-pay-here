<?php

namespace Database\Factories;

use ApiChef\PayHere\Tests\App\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductFactory extends Factory
{
    protected $model = Product::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->words(2, true),
            'price' => 100,
        ];
    }
}
