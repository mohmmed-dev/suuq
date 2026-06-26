<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

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
        $name = fake()->unique()->words(3, true);
        return [
            'name' => ucfirst($name),
            'slug' => Str::slug($name),
            'category_id' => Category::inRandomOrder()->first()?->id ?? Category::factory(),
            'description' => fake()->paragraphs(2, true),
            'price' => fake()->randomFloat(2, 5, 2000), // range from 5 to 2000
            'covers' => json_encode([
                fake()->imageUrl(640, 480, 'product', true),
                fake()->imageUrl(640, 480, 'product', true),
            ]),
            'stock' => fake()->numberBetween(0, 150),
            'is_active' => fake()->boolean(90), // 90% active
            'is_deleted' => false,
        ];
    }
}
