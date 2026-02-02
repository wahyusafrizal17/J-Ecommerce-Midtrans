<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Product>
 */
class ProductFactory extends Factory
{
    protected $model = Product::class;

    public function definition(): array
    {
        $name = fake()->unique()->words(3, true);

        return [
            'category_id' => Category::factory(),
            'name' => Str::title($name),
            'slug' => Str::slug($name) . '-' . fake()->unique()->randomNumber(4),
            'description' => fake()->optional()->paragraph(),
            'price_amount' => fake()->numberBetween(10_000, 1_500_000),
            'stock' => fake()->numberBetween(0, 100),
            'is_recommended' => fake()->boolean(25),
            'sold_count' => fake()->numberBetween(0, 200),
            'is_active' => true,
        ];
    }
}

