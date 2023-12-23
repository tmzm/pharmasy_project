<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Favorite extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function product()
    {
        return $this->hasOne(Product::class,'product_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class)->where('role','!=','warehouse_owner');
    }
}
