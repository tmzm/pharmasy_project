<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @method static byOrderAndProduct($id, $id1)
 */
class OrderItem extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $with = ['product'];

    public function scopeByProductAndUserId($query,$product_id,$user_id)
    {
        $query->whereHas('product',fn ($query) =>
            $query->where('id',$product_id)->whereHas('warehouse' , fn ($query)=>
            $query->where('user_id',$user_id)
            )
        );
    }

    public function scopeByOrderAndProduct($query,$order_id,$product_id)
    {
        $query->whereHas('order',fn($query)=>
        $query->where('id',$order_id)
        );

        $query->whereHas('product',fn($query)=>
        $query->where('id',$product_id)
        );
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
