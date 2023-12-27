<?php

namespace App\Http\Controllers;

use App\Enums\ReturnMessages;
use App\Models\OrderItem;
use Illuminate\Http\Response;

class OrderItemController extends Controller
{
    use ApiResponse;

    /**
     * Remove the specified resource from storage.
     * @param $product_id
     * @return Response
     */
    public function destroy($product_id): Response
    {
        $orderItem = OrderItem::whereHas('product',function ($query) use ($product_id){
            $query->where('id',$product_id);
        })->first();

        if($orderItem){
            $this->decresue_total_price_before_delete_order_item($orderItem);

            $orderItem->delete();

            return $this->apiResponse(200,ReturnMessages::Ok->value);
        }


        return $this->apiResponse(404,ReturnMessages::NotFound->value);
    }
}
