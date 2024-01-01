<?php

namespace Database\Seeders;

use App\Models\Category;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->createNewRecord('Vasolol',5,1,'png');
        $this->createNewRecord('Vento-Aid',9,1,'png');
        $this->createNewRecord('Uromax',2,1,'png');
        $this->createNewRecord('Smectal',3,1,'png');
        $this->createNewRecord('Otocalm',5,1,'png');
        $this->createNewRecord('Nailfen',4,1,'png');
        $this->createNewRecord('Artral Mineral',7,1,'png');
        $this->createNewRecord('Mentogel',8,1,'png');
        $this->createNewRecord('Diadap',1,1,'png');
        $this->createNewRecord('Colo Clean',3,2,'png');
        $this->createNewRecord('Cralyl',8,2,'png');
        $this->createNewRecord('Calcicomb',7,2,'png');
        $this->createNewRecord('New Aid',8,2,'png');
        $this->createNewRecord('Lukast',5,2,'png');
        $this->createNewRecord('Coteptal',9,2,'png');
        $this->createNewRecord('Vitamen-C Alfares',6,2,'png');
        $this->createNewRecord('Artral',7,2,'png');
        $this->createNewRecord('Azitrolyd',8,2,'png');

        $this->createNewRecord('Otocalm',5,3,'png');
        $this->createNewRecord('Nailfen',4,3,'png');
        $this->createNewRecord('Artral Mineral',7,3,'png');
        $this->createNewRecord('Mentogel',8,3,'png');
        $this->createNewRecord('Diadap',1,3,'png');
        $this->createNewRecord('Colo Clean',3,3,'png');
        $this->createNewRecord('Cralyl',8,3,'png');
        $this->createNewRecord('Calcicomb',7,3,'png');
        $this->createNewRecord('New Aid',8,3,'png');
    }
    public function createNewRecord($name,$cid,$wid,$ex){
        $maximumDate = Carbon::create(2025, 12, 31);
        $randomDate = Carbon::createFromTimestamp(mt_rand(time(), $maximumDate->timestamp));

        $imagePath = 'C:/Backups/test_images/'.$name.'.'.$ex;
        $imagName = time() . '_' . $name . '.' . $ex;
        copy($imagePath, public_path('images/' . $imagName));
        $image = '/images/' .  $imagName;

        \App\Models\Product::create([
            'scientific_name' => $name,
            'commercial_name' => $name,
            'company_name' => fake()->company(),
            'category_id' => $cid,
            'warehouse_id' => $wid,
            'image' => $image,
            'quantity'=>fake()->numberBetween(10,100),
            'price'=>fake()->numberBetween(1,7) * 5000,
            'expiration'=>$randomDate->format('Y-m-d')
        ]);
    }
}
