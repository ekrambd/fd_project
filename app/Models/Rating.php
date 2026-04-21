<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rating extends Model
{
    use HasFactory;

    protected $casts = [
        'orderdetail_id' => 'string',
        'user_id' => 'string',
        'rate' => 'string',
    ];

    public function orderdetail()
    {
    	return $this->belongsTo(Orderdetail::class);
    }
    
    public function user()
    {
    	return $this->belongsTo(User::class);
    }
}
