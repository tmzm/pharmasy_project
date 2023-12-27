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
    public function update_every_order_item_quantity($request): void
    {
        foreach ($request as $p) {
            $orderItem = OrderItem::where('product_id',$p['id']);
            $orderItem->update([
                'quantity' => $p['quantity']
            ]);
        }
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

    public function get_orders_by_warehouse_id($warehouseId)
    {
        return Order::whereHas('order_items', function ($query) use ($warehouseId) {
            $query->whereHas('product',function ($query)  use ($warehouseId){
                $query->where('warehouse_id',$warehouseId);
            });
        })->get();
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

    public function get_only_warehouse_product($product_id,$user_id)
    {
        return Product::whereHas('warehouse',function ($query) use ($product_id,$user_id){
            $query->where('user_id',$user_id);
        })->firstWhere('id',$product_id);
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
        if($request->user()->role == 'user')
            return Order::where('user_id',$request->user()->id)->get();
        else{
            $warehouseId = Warehouse::firstWhere('user_id',$request->user()->id)->id;

            return $this->get_orders_by_warehouse_id($warehouseId);
        }
    }
}
