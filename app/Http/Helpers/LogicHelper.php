<?php

namespace App\Http\Helpers;

use App\Models\Order;
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
    public function increase_every_product_by_quantity($order): void
    {
        $orderItems = $order->order_items;
        foreach ($orderItems as $item){
            $p = Product::find($item->product->id);
            $p->quantity += $item->quantity;
            $p->save();
        }
    }
    public function create_order_item_and_reduce_every_product_by_order_quantity($products,$order): void
    {
        $total_price = 0;
        foreach ($products as $p) {
            OrderItem::create([
                'product_id' => $p['id'],
                'order_id' => $order->id,
                'quantity' => $p['quantity']
            ]);

            $pr = Product::find($p['id']);
            $pr->quantity -= $p['quantity'];
            $total_price += ($pr->price)*$p['quantity'];
            $pr->save();
        }
        $order->total_price = $total_price;
        $order->save();
    }
    public function check_products_quantity($data): bool
    {
        foreach ($data['products'] as $p) {
            $product = Product::find($p['id']);
            if($product->quantity <= 0 || $product->quantity < $p['quantity'])
                return false;
        }
        return true;
    }

    public function decresue_total_price_before_delete_order_item($orderItem): void
    {
        $order = Order::whereHas('order_items',function ($query) use ($orderItem){
            $query->where('id',$orderItem->id);
        })->first();

        $product = $orderItem->product;

        $order->total_price -= $orderItem->quantity * $product->price;
        $order->save();

        $product->quantity += $orderItem->quantity;
        $product->save();
    }
}
