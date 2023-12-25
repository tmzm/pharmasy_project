<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Warehouse;
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
        return [
            'scientific_name'=>fake()->name(),
            'commercial_name'=>fake()->name(),
            'company_name'=>fake()->name(),
            'quantity'=>fake()->numberBetween(10,100),
            'price'=>5000,
            'category_id'=>Category::factory()->create()->id,
            'warehouse_id'=>Warehouse::factory()->create()->id,
            'expiration'=>fake()->dateTime()
        ];
    }
}
