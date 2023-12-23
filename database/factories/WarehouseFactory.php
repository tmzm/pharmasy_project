<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\WarehouseOwner;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Warehouse>
 */
class WarehouseFactory extends Factory
{

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name'=>fake()->name(),
            'location'=>fake()->sentence(),
            'image'=>'warehouses/image.png',
            'user_id'=>User::factory()->create([
                'role'=>'warehouse_owner'
            ])->id
        ];
    }
}
