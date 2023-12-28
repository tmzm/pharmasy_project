<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class Order extends Model
{
    use HasFactory;

    public function scopeByWarehouseIdOrUser($query,$warehouse_id,Request $request)
    {
        if($request->user()->role == 'user')
            $query->where('user_id',$request->user()->id);
        else {
            $query->whereHas('order_items', fn($query) => $query->whereHas('product', fn($query) => $query->where('warehouse_id', $warehouse_id))
            );
        }
    }

    public function scopeByOrderItemId($query,$order_item_id)
    {
       $query->whereHas('order_items',fn ($query) =>
            $query->where('id',$order_item_id)
        );
    }

    protected $guarded = [];

    protected $with = ['user','order_items'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function order_items()
    {
        return $this->hasMany(OrderItem::class);
    }
}
