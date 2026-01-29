<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    public function orderdetail()
    {
    	return $this->belongsTo(Orderdetail::class);
    }

    public function item()
    {
    	return $this->belongsTo(Item::class);
    }
}
