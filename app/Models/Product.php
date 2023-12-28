<?php

namespace App\Models;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $with = ['category'];

    protected $guarded = [];

    /**
     * @param $query
     * @param array $filters
     * @param string $warehouse_id
     */
    public function scopeFilter($query, array $filters,string $warehouse_id){

        if($filters['search'] ?? false){

            $query->where(
                fn($query)=>
                $query
                    ->where('scientific_name', 'like', '%' . $filters['search'] . '%')
                    ->orWhere('commercial_name', 'like', '%' . $filters['search'] . '%')
            );

        }

        if($filters['category'] ?? false){

            $query->whereHas('category', fn ($query)

            => $query->where('name',$filters['category'])

            );

        }

        if($warehouse_id ?? false){

            $query->whereHas('warehouse', fn ($query)

            => $query->where('id',$warehouse_id)

            );

        }

    }

    public function scopeByOwnerAndProductId($query,$user_id,$product_id)
    {
        $query->whereHas('warehouse',fn ($query) =>
            $query->where('user_id',$user_id)->firstWhere('id',$product_id)
        );
    }

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function order_item()
    {
        return $this->hasOne(OrderItem::class);
    }

}
