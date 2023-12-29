<?php

namespace App\Http\Controllers;

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
