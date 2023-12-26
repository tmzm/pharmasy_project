<?php

namespace App\Http\Controllers;

use App\Enums\ReturnMessages;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Warehouse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;
use App\Notifications\InvoicePaid;

class OrderController extends Controller
{
    use ApiResponse;

    /**
     * Display a listing of the resource.
     * @param Request $request
     * @return Response
     */
    public function index(Request $request): Response
    {
        if($request->user()->role == 'user')
            $orders = Order::where('user_id',$request->user()->id)->get();
        else{
            $warehouseId = Warehouse::firstWhere('user_id',$request->user()->id)->id;

            $orders = Order::whereHas('order_items', function ($query) use ($warehouseId) {
                $query->whereHas('product',function ($query)  use ($warehouseId){
                    $query->where('warehouse_id',$warehouseId);
                });
            })->get();
        }

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

        $order = Order::create([
            'user_id' => $request->user()->id
        ]);

        foreach ($data['products'] as $p) {
            if(Product::find($p['id'])->quantity <= 0)
                return $this->apiResponse(500,ReturnMessages::Error->value);
        }

        foreach ($data['products'] as $p) {
            OrderItem::create([
                'product_id' => $p['id'],
                'order_id' => $order->id,
                'quantity' => $p['quantity']
            ]);

            $pr = Product::find($p['id']);
            $pr->quantity -= 1;
            $pr->save();
        }

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

        $order->update([
            'status' => $request['status'],
            'payment_status' => $request['payment_status']
        ]);

        if($request['products'])
        foreach ($request['products'] as $p) {
            $orderItem = OrderItem::where('product_id',$p['id']);
            $orderItem->update([
                'quantity' => $p['quantity']
            ]);
        }

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
            $orderItems = $order->order_items;
            foreach ($orderItems as $item){
                $p = Product::find($item->product->id);
                $p->quantity += 1;
                $p->save();
            }

            $order->delete();

            return $this->apiResponse(200,ReturnMessages::Ok->value);
        }

        return $this->apiResponse(404,ReturnMessages::NotFound->value);
    }
}
