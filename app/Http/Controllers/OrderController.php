<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     * @param Request $request
     */
    public function index(Request $request)
    {
        self::get_user_or_warehouse_orders($request);
    }

    /**
     * Show the form for creating a new resource.
     * @param Request $request
     */
    public function create(Request $request)
    {
        self::create_order_by_request($request);
    }

    /**
     * Display the specified resource.
     * @param Request $request
     * @param $order_id
     */
    public function show(Request $request,$order_id): void
    {
        self::get_user_order_by_id($order_id,$request->user()->id);
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param $order_id
     */
    public function update(Request $request, $order_id): void
    {
        self::update_order_by_request_and_order($request,$order_id);
    }

    /**
     * Remove the specified resource from storage.
     * @param Request $request
     * @param $order_id
     */
    public function destroy(Request $request,$order_id): void
    {
         self::delete_order($request,$order_id);
    }
}
