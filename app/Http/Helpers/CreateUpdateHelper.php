<?php

namespace App\Http\Helpers;

use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use App\Models\Warehouse;

trait CreateUpdateHelper
{
    public function update_order_status(Order $order,$request) : void
    {
        $order->update([
            'status' => $request['status'],
            'payment_status' => $request['payment_status']
        ]);
    }

    public function create_order($user_id)
    {
        return Order::create([
            'user_id' => $user_id,
        ]);
    }

    public function create_warehouse($d,$user_id)
    {
        return Warehouse::create([
            'name' => $d['warehouseName'],
            'location' => $d['location'],
            'image' => $d['image'],
            'user_id' => $user_id
        ]);
    }

    public function create_user($data)
    {
        return User::create([
            'name' => $data['name'],
            'phone_number' => $data['phone_number'],
            'password' => bcrypt($data['password']),
            'role' => $data['role']
        ]);
    }

    public function create_order_by_request_and_product($request): mixed
    {
        $data = $request->validated();

        $products = $data['products'];

        if(!self::check_products_quantity($products))
            return false;

        $order = self::create_order($request->user()->id);
        self::create_order_item_and_reduce_every_product_by_order_quantity($products,$order);
        return $order;
    }

    public function update_order_by_request_and_order($request,$order): bool
    {
        if($request['status'] ?? $request['payment_status'] ?? false)
            self::update_order_status($order,$request);

        if($request['products'] ?? false)
            if(!self::update_every_order_item_quantity($request['products'],$order))
                return false;

        return true;
    }

    public function delete_order($order): bool
    {
        if($order) {
            self::increase_every_product_by_quantity($order);
            $order->delete();
            return true;
        }
        return false;
    }

    public function delete_order_item($orderItem): bool
    {
        if($orderItem){
            self::decresue_total_price_before_delete_order_item($orderItem);
            $orderItem->delete();
            return true;
        }
        return false;
    }

    public function create_product($request)
    {
        $data = $request->validated();

        $data['warehouse_id'] = Warehouse::where('user_id',$request->user()->id)->first()->id;

        $image = self::save_image_to_public_directory($request);

        if($image !== false)
            $data['image'] = $image;

        return Product::create($data);
    }

    public function update_product($request,$product)
    {
        $data = $request->validated();

        $image = self::save_image_to_public_directory($request);

        if($image !== false)
            $data['image'] = $image;

        return $product->update($data);
    }

}
