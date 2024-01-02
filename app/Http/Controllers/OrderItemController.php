<?php

namespace App\Http\Controllers;

class OrderItemController extends Controller
{
    /**
     * Remove the specified resource from storage.
     * @param $orderitem_id
     */
    public function destroy($orderitem_id)
    {
        self::delete_order_item($orderitem_id);
    }
}
