<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \App\Models\Category::create([
            'name'=> 'Diabetes'
        ]);
        \App\Models\Category::create([
            'name'=> 'Urinary'
        ]);
        \App\Models\Category::create([
            'name'=> 'Digestive'
        ]);
        \App\Models\Category::create([
            'name'=> 'Dermal'
        ]);
        \App\Models\Category::create([
            'name'=> 'Respiratory'
        ]);
        \App\Models\Category::create([
            'name'=> 'Vitamins'
        ]);
        \App\Models\Category::create([
            'name'=> 'Alimentary'
        ]);
        \App\Models\Category::create([
            'name'=> 'Antibiotics'
        ]);
        \App\Models\Category::create([
            'name'=> 'Pressure'
        ]);
    }
}
