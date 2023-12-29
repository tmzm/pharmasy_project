<?php

namespace App\Http\Helpers;

use App\Models\Product;
use Exception;
use Illuminate\Http\Request;

trait LogicHelper
{
    public function save_image_to_public_directory(Request $request): bool|string
    {
        try{
            if ($request->hasfile('image')) {
                $image = $request->file('image');
                $imageName = time().'.'.$request->file('image')->getClientOriginalExtension();
                copy($image, public_path('images/' . $imageName));
                return '/images/' .  $imageName;
            }
        }catch(Exception $e){
            return false;
        }
        return false;
    }

    public function check_products_quantity($data): bool
    {
        foreach ($data as $p) {
            $product = Product::find($p['id']);
            if($product->quantity <= 0 || $product->quantity < $p['quantity'])
                return false;
        }
        return true;
    }
}
