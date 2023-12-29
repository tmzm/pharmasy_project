<?php

namespace App\Http\Controllers;

use App\Enums\ReturnMessages;
use App\Models\OrderItem;
use Illuminate\Http\Response;

class OrderItemController extends Controller
{
    /**
     * Remove the specified resource from storage.
     * @param $order_item_id
     * @return Response
     */
    public function destroy($order_item_id): Response
    {
        $orderItem = OrderItem::find($order_item_id);

        return self::delete_order_item($orderItem) ? self::apiResponse(200, ReturnMessages::Ok->value) : self::apiResponse(404, ReturnMessages::NotFound->value);
    }
}
