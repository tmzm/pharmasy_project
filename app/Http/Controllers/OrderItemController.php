<?php

namespace App\Http\Controllers;

class OrderItemController extends Controller
{
    /**
     * Remove the specified resource from storage.
     * @param $orderItem_id
     */
    public function destroy($orderItem_id)
    {
        self::delete_order_item($orderItem_id);
    }
}
