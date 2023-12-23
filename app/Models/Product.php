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
     * @param string $warehouse
     */
    public function scopeFilter($query, array $filters,string $warehouse){

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

        if($warehouse ?? false){

            $query->whereHas('warehouse', fn ($query)

            => $query->where('name',$warehouse)

            );

        }

    }

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);

    }

}
