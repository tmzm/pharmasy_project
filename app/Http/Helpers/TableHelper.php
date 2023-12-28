<?php

namespace App\Http\Helpers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use App\Models\Warehouse;
use Illuminate\Http\Request;

trait TableHelper
{
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

    public function get_request_warehouse_id_by_role(Request $request)
    {
        if ($request->user()->role == 'user')
            return request('warehouse_id');
        else {
            return Warehouse::firstWhere('user_id',$request->user()->id)->id;
        }
    }

    public function filter_products($filters ,Request $request)
    {
        $warehouse_id = $this->get_request_warehouse_id_by_role($request);

        return Product::filter($filters,$warehouse_id)->latest()->get();
    }

    public function get_user_orders_or_warehouse_orders(Request $request)
    {
        return Order::byWarehouseIdOrUser(Warehouse::firstWhere('user_id',$request->user()->id)?->id,$request)->get();
    }
}
