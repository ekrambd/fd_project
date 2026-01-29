<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    use HasFactory;

    protected $appends = ['discount_price'];

    public function category()
    {
    	return $this->belongsTo(Category::class);
    }

    public function unit()
    {
    	return $this->belongsTo(Unit::class);
    }

    public function orders()
    {
    	return $this->hasMany(Order::class);
    }

    public function getDiscountPriceAttribute()
    {
        $price = itemPrice($this->id);
        return strval($price);
    }
}
