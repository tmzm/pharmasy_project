<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Warehouse;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class WarehouseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->createNewRecord('Fares');
        $this->createNewRecord('IbnSina');
        $this->createNewRecord('PharmaOffer');
    }

    public function createNewRecord($name){
        $imagePath = 'C:/Backups/test_images/' . $name . '.png';
        $imagName = time() . '_' . $name . '.png';
        copy($imagePath, public_path('images/' . $imagName));
        $image = '/images/' .  $imagName;

        $id = User::create([
            'name'=>$name,
            'phone_number' => fake()->unique()->phoneNumber(),
            'password' => bcrypt('password'),
            'remember_token' => Str::random(10),
            'role' => 'warehouse_owner'
        ])->id;
        Warehouse::create([
            'name' => $name . ' warehouse',
            'location' => $name . ' location',
            'user_id' => $id,
            'image' => $image
        ]);
    }
}
