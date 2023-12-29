<?php

namespace App\Http\Helpers;

use App\Models\Favorite;
use App\Models\Order;
use App\Models\OrderItem;
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

    public function decresue_total_price_before_delete_order_item($orderItem): void
    {
        $order = Order::byOrderItemId($orderItem->id)->first();
        $product = $orderItem->product;
        $order->total_price -= $orderItem->quantity * $product->price;
        $order->save();
        $product->quantity += $orderItem->quantity;
        $product->save();
    }

    public function update_every_order_item_quantity($request,$order): bool
    {
        // $temp = $request;
        // check new order quantity if not biggest than product quantity
        foreach ($request as $p){
            $orderItem = OrderItem::firstWhere('product_id',$p['id']);
            $product = Product::find($p['id']);
            $product->quantity += $orderItem->quantity;
            if($product->quantity < $p['quantity'])
                return false;
        }
        foreach ($request as $p) {
            $orderItem = OrderItem::firstWhere('product_id',$p['id']);
            $product = Product::find($p['id']);
            $product->quantity += $orderItem->quantity;
            $order->total_price -= $orderItem->quantity * $product->price;
            $orderItem->update([
                'quantity' => $p['quantity']
            ]);
            $order->total_price += $p['quantity'] * $product->price;
            $order->save();
            $product->quantity -= $p['quantity'];
            $product->save();
        }

        return true;
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

    public function create_order_by_request($request): mixed
    {
        $data = $request->validated();

        $products = $data['products'];

        if(!self::check_products_quantity($products))
            self::unHandledError();

        $order = self::create_order($request->user()->id);
        self::create_order_item_and_reduce_every_product_by_order_quantity($products,$order);
        self::ok($order);
    }

    public function update_order_by_request_and_order($request,$order_id): void
    {
        $order = Order::find($order_id);

        if(!$order)
            self::notFound();

        if($request['status'] ?? $request['payment_status'] ?? false)
            self::update_order_status($order,$request);

        if($request['products'] ?? false)
            if(!self::update_every_order_item_quantity($request['products'],$order))
                self::unHandledError();

        self::ok($order);
    }

    public function delete_order($request,$order_id): void
    {
        $order = Order::find($order_id)?->firstWhere('user_id',$request->user()->id);

        if($order) {
            self::increase_every_product_by_quantity($order);
            $order->delete();
            self::ok();
        }
        self::notFound();
    }

    public function delete_order_item($order_item_id): bool
    {
        $orderItem = OrderItem::find($order_item_id);

        if($orderItem){
            self::decresue_total_price_before_delete_order_item($orderItem);
            $orderItem->delete();
            self::ok();
        }
        self::notFound();
    }

    public function create_product($request)
    {
        $data = $request->validated();

        $data['warehouse_id'] = Warehouse::where('user_id',$request->user()->id)->first()->id;

        $image = self::save_image_to_public_directory($request);

        if($image !== false)
            $data['image'] = $image;

        self::ok(Product::create($data));
    }

    public function update_product($request,$product_id)
    {
        $product = Product::byOwnerAndProductId($product_id,$request->user()->id)->first();

        if(!$product)
            self::notFound();

        $data = $request->validated();

        $image = self::save_image_to_public_directory($request);

        if($image !== false)
            $data['image'] = $image;

        $product->update($data);

        self::ok($product);
    }

    public function delete_product($request,$product_id): void
    {
        $product = Product::byOwnerAndProductId($request->user()->id,$product_id);

        if($product) {
            $product->delete();

            self::ok();
        }

        self::notFound();
    }

    public function create_favorite($user_id,$product_id): void
    {
        $favorite = Favorite::create([
            'product_id' => $product_id,
            'user_id' => $user_id
        ]);

        $favorite ? self::ok($favorite) : self::unHandledError();
    }

    public function delete_user_favorite($favorite_id,$user_id): void
    {
        $favorite = Favorite::find($favorite_id)->firstWhere('user_id',$user_id);

        if($favorite) {
            $favorite->delete();
            self::ok();
        }

        self::notFound();
    }

}
