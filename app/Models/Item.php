<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    use HasFactory;

    protected $appends = ['discount_price'];

    protected $casts = [
        'user_id' => 'string',
        'category_id' => 'string',
        'unit_id' => 'string',
    ];

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
