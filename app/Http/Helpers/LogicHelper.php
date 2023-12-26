<?php

namespace App\Http\Helpers;

use App\Models\OrderItem;
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
    public function decresue_every_product_by_one($order): void
    {
        $orderItems = $order->order_items;
        foreach ($orderItems as $item){
            $p = Product::find($item->product->id);
            $p->quantity += 1;
            $p->save();
        }
    }
    public function create_order_item_and_reduce_every_product_by_one($products,$order): void
    {
        $total_price = 0;
        foreach ($products as $p) {
            OrderItem::create([
                'product_id' => $p['id'],
                'order_id' => $order->id,
                'quantity' => $p['quantity']
            ]);

            $pr = Product::find($p['id']);
            $pr->quantity -= 1;
            $total_price += $pr->price;
            $pr->save();
        }
        $order->total_price = $total_price;
        $order->save();
    }
    public function check_products_quantity($data): bool
    {
        foreach ($data['products'] as $p) {
            if(Product::find($p['id'])->quantity <= 0)
                return false;
        }
        return true;
    }

}
