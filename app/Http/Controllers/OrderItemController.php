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
     */
    public function destroy($order_item_id)
    {
        self::delete_order_item($order_item_id);
    }
}
