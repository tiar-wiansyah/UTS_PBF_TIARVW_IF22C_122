<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
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
        $category = Category::factory()->create();
        $user = User::factory()->create();

        return [
            'name' => fake()->name(),
            'description' => 'ff',
            'category_id' => $category->id,
            'price' => fake()->numberBetween(1000, 100000),
            'modified_by' => $user->id,
            'expired_at' => fake()->date(),
            'image' => '/uploads/images/products/default.png',
        ];
    }
}
