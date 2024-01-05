<?php

namespace App\Http\Helpers;

use App\Models\Category;
use App\Models\Favorite;
use App\Models\Order;
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

    public function filter_products($filters ,Request $request): void
    {
        $products = Product::filter($filters, self::get_request_warehouse_id_by_role($request))->latest()->get();

        count($products) ? self::ok($products) : self::notFound();
    }

    public function get_product_by_id($product_id): void
    {
        $product = Product::find($product_id);

        $product ? self::ok($product) : self::notFound();
    }

    public function get_user_or_warehouse_orders(Request $request): void
    {
        $orders = Order::byWarehouseIdOrUser(Warehouse::firstWhere('user_id',$request->user()->id)?->id,$request)->latest()->get();

        if(count($orders))
            self::ok($orders);

        self::notFound();
    }

    public function get_user_order_by_id($order_id,$user_id): void
    {
        $order = Order::where('id',$order_id)?->firstWhere('user_id',$user_id);

        $order ? self::ok($order) : self::notFound();
    }

    public function get_all_favorites_by_user($request): void
    {
        $favorites = Favorite::latest()->where('user_id',$request->user()->id)->get();

        count($favorites) ? self::ok($favorites) : self::notFound();
    }

    public function get_user_favorite_by_id($favorite_id,$user_id): void
    {
        $favorite = Favorite::find($favorite_id)?->firstWhere('user_id',$user_id);

        $favorite ? self::ok($favorite) : self::notFound();
    }

    public function get_all_categories(): void
    {
        $categories = Category::get();

        count($categories) ? self::ok($categories) : self::notFound();
    }

    public function get_warehouse_by_id($warehouse_id): void
    {
        $warehouse = Warehouse::find($warehouse_id);

        $warehouse ? self::ok($warehouse) : self::notFound();
    }
}
