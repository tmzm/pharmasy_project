<?php

namespace App\Http\Controllers;

use App\Enums\ReturnMessages;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use App\Models\Warehouse;
use Illuminate\Http\Request;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Foundation\Application as Application2;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Notification;
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

            $orders = Order::whereHas('product', function ($query) use ($warehouseId) {
                $query->where('warehouse_id', $warehouseId);
            })->get();
        }

        if(count($orders))
            return $this->apiResponse(200,ReturnMessages::Ok->value,$orders);

        return $this->apiResponse(404,ReturnMessages::NotFound->value);
    }

    /**
     * Show the form for creating a new resource.
     * @param Request $request
     * @param $product_id
     * @return Response
     * @throws ValidationException
     */
    public function create(Request $request, $product_id): Response
    {
        $product = Product::find($product_id);

        if(!$product)
            return $this->apiResponse(404,ReturnMessages::NotFound->value);

        $validator = validator($request->all(),[
           'quantity' => 'required',
        ]);

        if($validator->fails())
            return $this->apiResponse(500,ReturnMessages::ValidateError->value,null,null,$validator->errors());

        $data = $validator->validated();

        if(!$request['order_id']){
            $order = Order::create([
                'user_id' => $request->user()->id
            ]);
        }else{
            $order = Order::find($request['order_id']);
        }

        if(!$order)
            return $this->apiResponse(404,ReturnMessages::NotFound->value);

        OrderItem::create([
            'product_id' => $product_id,
            'order_id' => $order->id,
            'quantity' => $data['quantity']
        ]);

        $product->quantity -= 1;
        $product->save();

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
    public function update(Request $request,$order_id): Response
    {
        $order = Order::find($order_id);

        if($order) {
            $validator = validator($request->all(),[
                'status' => '',
                'payment_status' => ''
            ]);

            if($validator->fails())
                return $this->apiResponse(500,ReturnMessages::ValidateError->value,null,null,$validator->errors());

            $data = $validator->validated();

            $order->update($data);

            if($request['order_item_id']){
                $order_item = OrderItem::firstWhere('id',$request['order_item_id']);

                $validator = validator($request->all(),[
                    'quantity' => ''
                ]);

                if($validator->fails())
                    return $this->apiResponse(500,ReturnMessages::ValidateError->value,null,null,$validator->errors());

                $data = $validator->validated();

                $order_item->update($data);
            }

            $order = Order::find($order_id);

            return $this->apiResponse(200,ReturnMessages::Ok->value,$order);
        }

        return $this->apiResponse(404,ReturnMessages::NotFound->value);
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
            $products = $order->order_items()->select('product');
            foreach ($products as $p){
                $p->quantity++;
                $p->save();
            }

            $order->delete();

            return $this->apiResponse(200,ReturnMessages::Ok->value);
        }

        return $this->apiResponse(404,ReturnMessages::NotFound->value);
    }
}
