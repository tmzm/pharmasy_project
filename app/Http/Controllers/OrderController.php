<?php

namespace App\Http\Controllers;

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
        if($request->user()->role == 'user'){
            $orders = Order::where('user_id',$request->user()->id)->get();

            if($orders)
                 return $this->apiResponse(200,'ok',$orders);

            return $this->apiResponse(404,'No data found');
        }else{
            $warehouseId = Warehouse::firstWhere('user_id',$request->user()->id)->id;

            $orders = Order::whereHas('product', function ($query) use ($warehouseId) {
                $query->where('warehouse_id', $warehouseId);
            })->get();

            if($orders)
                return $this->apiResponse(200,'ok',$orders);

            return $this->apiResponse(404,'No data found');
        }
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
            return $this->apiResponse(404,'Product not found');

        $validator = validator($request->all(),[
           'quantity' => 'required',
        ]);

        if($validator->fails())
            return $this->apiResponse(500,'validate has errors',null,null,$validator->errors());

        $data = $validator->validated();

        if(!$request['order_id']){
            $order = Order::create([
                'user_id' => $request->user()->id
            ]);
        }else{
            $order = Order::find($request['order_id']);
        }

        OrderItem::create([
            'product_id' => $product_id,
            'order_id' => $order->id,
            'quantity' => $data['quantity']
        ]);

        return $this->apiResponse(200,'ok',$order);
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
            return $this->apiResponse(200,'ok',$order);

        return $this->apiResponse(404,'Not found');
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
                return $this->apiResponse(500,'validate has errors',null,null,$validator->errors());

            $data = $validator->validated();

            $order->update($data);

            if($request['order_item_id']){
                $order_item = OrderItem::firstWhere('id',$request['order_item_id']);

                $validator = validator($request->all(),[
                    'quantity' => ''
                ]);

                if($validator->fails())
                    return $this->apiResponse(500,'validate has errors',null,null,$validator->errors());

                $data = $validator->validated();

                $order_item->update($data);
            }

            $order = Order::find($order_id);

            return $this->apiResponse(201,'ok',$order);
        }

        return $this->apiResponse(404,'Not found');
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
            $order->delete();

            return $this->apiResponse(200,'ok');
        }

        return $this->apiResponse(404,'Not found');
    }
}
