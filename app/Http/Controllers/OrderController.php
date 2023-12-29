<?php

namespace App\Http\Controllers;

use App\Enums\ReturnMessages;
use App\Http\Requests\CreateOrderRequest;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     * @param Request $request
     * @return Response
     */
    public function index(Request $request): Response
    {
        $orders = self::get_user_orders_or_warehouse_orders($request);

        if(count($orders))
            return self::apiResponse(200,ReturnMessages::Ok->value,$orders);

        return self::apiResponse(404,ReturnMessages::NotFound->value);
    }

    /**
     * Show the form for creating a new resource.
     * @param CreateOrderRequest $request
     * @return Response
     */
    public function create(CreateOrderRequest $request): Response
    {
        $order = self::create_order_by_request_and_product($request);

        return $order === false ? self::apiResponse(500, ReturnMessages::Error->value) : self::apiResponse(200, ReturnMessages::Ok->value, $order);
    }

    /**
     * Display the specified resource.
     * @param Request $request
     * @param $order_id
     * @return Response
     */
    public function show(Request $request,$order_id): Response
    {
        $order = Order::where('id',$order_id)?->firstWhere('user_id',$request->user()->id);

        return $order ? self::apiResponse(200, ReturnMessages::Ok->value, $order) : self::apiResponse(404, ReturnMessages::NotFound->value);
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param $order_id
     * @return Response
     * @throws ValidationException
     */
    public function update(Request $request, $order_id): Response
    {
        $order = Order::find($order_id);

        if(!$order)
            return self::apiResponse(404,ReturnMessages::NotFound->value);

        if(!self::update_order_by_request_and_order($request,$order))
            return self::apiResponse(500,ReturnMessages::Error->value);

        $order = Order::find($order_id);

        return self::apiResponse(200,ReturnMessages::Ok->value,$order);
    }

    /**
     * Remove the specified resource from storage.
     * @param Request $request
     * @param $order_id
     * @return Response
     */
    public function destroy(Request $request,$order_id): Response
    {
        $order = Order::find($order_id)?->firstWhere('user_id',$request->user()->id);

        return self::delete_order($order) ? self::apiResponse(200, ReturnMessages::Ok->value) : self::apiResponse(404, ReturnMessages::NotFound->value);
    }
}
