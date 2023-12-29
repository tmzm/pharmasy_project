<?php

namespace App\Http\Helpers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Warehouse;
use Illuminate\Http\Request;

trait TableGetterHelper
{
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
        return Product::filter($filters, self::get_request_warehouse_id_by_role($request))->latest()->get();
    }

    public function get_user_orders_or_warehouse_orders(Request $request)
    {
        return Order::byWarehouseIdOrUser(Warehouse::firstWhere('user_id',$request->user()->id)?->id,$request)->get();
    }
}
