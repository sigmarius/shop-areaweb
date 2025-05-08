<?php

namespace Database\Factories;

use App\Enums\ProductStatusEnum;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->sentence(),
            'description' => fake()->text(100),
            'count' => fake()->randomNumber(2),
            'price' => fake()->randomNumber(3),
            'status' => fake()->randomElement(ProductStatusEnum::cases())->value,
        ];
    }
}
