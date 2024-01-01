<?php

namespace App\Http\Helpers;

use App\Models\Product;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

trait LogicHelper
{
    public function save_image_to_public_directory(Request $request): bool|string
    {
        try{
            if ($request->hasfile('image')) {
                $image = $request->file('image');
                $imageName = time().'_'.$request->file('image')->getBasename().'.'.$request->file('image')->getClientOriginalExtension();
                copy($image, public_path('images/' . $imageName));
                return '/images/' .  $imageName;
            }
        }catch(Exception $e){
            return false;
        }
        return false;
    }

    public function delete_image($image_path): void
    {
        if (File::exists($image_path)) {
            File::delete($image_path);
        }
    }

    public function check_products_quantity($data): void
    {
        foreach ($data as $p) {
            $product = Product::find($p['id']);
            if(!$product || $product->quantity <= 0 || $product->quantity < $p['quantity'])
                self::unHandledError();
        }
    }
}
