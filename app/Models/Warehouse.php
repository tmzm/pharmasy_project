<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Warehouse extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $with = ['warehouse_owner'];

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public function warehouse_owner()
    {
        return $this->belongsTo(user::class,'user_id');
    }
}
