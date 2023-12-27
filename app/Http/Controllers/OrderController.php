<?php

namespace App\Http\Controllers;

use App\Enums\ReturnMessages;
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
        $orders = $this->get_user_orders_or_warehouse_orders($request);

        if(count($orders))
            return $this->apiResponse(200,ReturnMessages::Ok->value,$orders);

        return $this->apiResponse(404,ReturnMessages::NotFound->value);
    }

    /**
     * Show the form for creating a new resource.
     * @param Request $request
     * @return Response
     * @throws ValidationException
     */
    public function create(Request $request): Response
    {
        $validator = validator($request->all(),[
           'products' => 'required',
        ]);

        if($validator->fails())
            return $this->apiResponse(500,ReturnMessages::ValidateError->value,null,null,$validator->errors());

        $data = $validator->validated();

        if($this->check_products_quantity($data) === false)
            return $this->apiResponse(500,ReturnMessages::Error->value);

        $order = $this->create_order($request->user()->id);

        $this->create_order_item_and_reduce_every_product_by_order_quantity($data['products'],$order);

        return $this->apiResponse(200,ReturnMessages::Ok->value,$order);
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

        if($order)
            return $this->apiResponse(200,ReturnMessages::Ok->value,$order);

        return $this->apiResponse(404,ReturnMessages::NotFound->value);
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
            return $this->apiResponse(404,ReturnMessages::NotFound->value);

        $this->update_order_status($order,$request);

        if($request['products'])
            $this->update_every_order_item_quantity($request['products']);

        $order = Order::find($order_id);

        return $this->apiResponse(200,ReturnMessages::Ok->value,$order);
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

        if($order) {
            $this->increase_every_product_by_quantity($order);

            $order->delete();

            return $this->apiResponse(200,ReturnMessages::Ok->value);
        }

        return $this->apiResponse(404,ReturnMessages::NotFound->value);
    }
}
