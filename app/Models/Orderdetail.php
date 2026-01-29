<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Orderdetail extends Model
{
    use HasFactory;
        
    public function orders()
    {
    	return $this->hasMany(Order::class);
    }

    public function paymentmethod()
    {
    	return $this->belongsTo(Paymentmethod::class);
    }
    
    public function rating()
    {
    	return $this->hasOne(Rating::class);
    }
}
