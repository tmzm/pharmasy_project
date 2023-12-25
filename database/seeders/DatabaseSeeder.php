<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Category;
use Database\Factories\CategoryFactory;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        (new CategorySeeder)->run();
        \App\Models\Product::factory()->create([
            'scientific_name' => 'Vasolo',
            'commercial_name' => 'Vasolol',
            'category_id' => 1,
            'warehouse_id' => 1,
        ]);
        \App\Models\Product::factory()->create([

        ]);
        \App\Models\Product::factory()->create([

        ]);
        \App\Models\Product::factory()->create([

        ]);
        \App\Models\Product::factory()->create([

        ]);
        \App\Models\Product::factory()->create([

        ]);
        \App\Models\Product::factory()->create([

        ]);
        \App\Models\Product::factory()->create([

        ]);
        \App\Models\Product::factory()->create([

        ]);
        \App\Models\Product::factory()->create([

        ]);
        \App\Models\Product::factory()->create([

        ]);
        \App\Models\Product::factory()->create([

        ]);
        \App\Models\Product::factory()->create([

        ]);
        \App\Models\Product::factory()->create([

        ]);
        \App\Models\Product::factory()->create([

        ]);
        \App\Models\Product::factory()->create([

        ]);
        \App\Models\Product::factory()->create([

        ]);
        \App\Models\Product::factory()->create([

        ]);
        \App\Models\Product::factory()->create([

        ]);
        \App\Models\Product::factory()->create([

        ]);

    }
}
