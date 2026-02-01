<?php

use App\Models\Category;
use App\Models\Package;
use App\Models\Unit;
use App\Models\Item;
use App\Models\Orderdetail;
use App\Models\Restaurant;

function user()
{
	$user = auth()->user();
	return $user;
}

function makeOrderNo()
{
	$count = Orderdetail::count();
	$count+=1;
	$invoiceNo = "Order-00".$count;
	return $invoiceNo;
}


function categories()
{
	$categories = Category::latest()->get();
	return $categories;
}

function units()
{
	$units = Unit::where('status','Active')->latest()->get();
	return $units;
}

function item($id)
{
	$item = Item::find($id);
	return $item;
}

function itemPrice($id)
{
	$item = item($id);

	$original_price = $item->item_price; 
    $discount_rate = $item->item_discount/100; // % discount expressed as a decimal

    $discount_amount = $original_price * $discount_rate;

    $finalAmount = $item->item_price - $discount_amount;

    return $finalAmount;
}

function restaurant()
{
	$data = Restaurant::find(1);
	return $data;
}

function setReadNotify($id)
{
	$notification = auth()->user()->notifications()->where('order_id', $id)->first();
    if($notification) {
        $notification->markAsRead();
    }
}

function sendOTP($phone,$otp)
{
	$curl = curl_init();

	curl_setopt_array($curl, array(
	  CURLOPT_URL => "http://sms.songbirdtelecom.com:8746/sendtext?apikey=4a247b18cfc78410&secretkey=25a8ee98&callerID=8801847&toUser={$phone}&messageContent=TheClaysBD: Your OTP is {$otp}. Please do not share this code with anyone.",
	  CURLOPT_RETURNTRANSFER => true,
	  CURLOPT_ENCODING => '',
	  CURLOPT_MAXREDIRS => 10,
	  CURLOPT_TIMEOUT => 0,
	  CURLOPT_FOLLOWLOCATION => true,
	  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
	  CURLOPT_CUSTOMREQUEST => 'GET',
	));

	$response = curl_exec($curl);

	$result = json_decode($response,true);

	return $result;
}