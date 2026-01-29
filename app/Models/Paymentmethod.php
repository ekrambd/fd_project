<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Paymentmethod extends Model
{
    use HasFactory;

    public function orderdetails()
    {
    	return $this->hasMany(Orderdetail::class);
    }
}
