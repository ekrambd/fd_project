<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Unit;
use App\Models\Item;
use App\Models\Orderdetail;
use Illuminate\Support\Facades\Http;
use Firebase\JWT\JWT;
use App\Models\User;

class AjaxController extends Controller
{
    public function categoryStatusUpdate(Request $request)
    {
    	try
    	{
    		$category = Category::findorfail($request->category_id);
    		$category->status = $request->status;
    		$category->update();
    		return response()->json(['status'=>true, 'message'=>"Successfully the category's status has been updated"]);
    	}catch(Exception $e){
            return response()->json(['status'=>false, 'code'=>$e->getCode(), 'message'=>$e->getMessage()],500);
        }
    }

    public function unitStatusUpdate(Request $request)
    {
        try
        {
            $unit = Unit::findorfail($request->unit_id);
            $unit->status = $request->status;
            $unit->update();
            return response()->json(['status'=>true, 'message'=>"Successfully the unit's status has been updated"]);

        }catch(Exception $e){
            return response()->json(['status'=>false, 'code'=>$e->getCode(), 'message'=>$e->getMessage()],500);
        }
    }

    public function itemStatusUpdate(Request $request)
    {
        try
        {
            $item = Item::findorfail($request->item_id);
            $item->status = $request->status;
            $item->update();
            return response()->json(['status'=>true, 'message'=>"Successfully the item's status has been updated"]);
        }catch(Exception $e){
            return response()->json(['status'=>false, 'code'=>$e->getCode(), 'message'=>$e->getMessage()],500);
        }
    }

    // public function orderStatusUpdate(Request $request)
    // {
    //     try
    //     {
    //         $order = Orderdetail::findorfail($request->order_id);
    //         $order->status = $request->status;
    //         $order->update();
    //         // Http::post('http://localhost:3000/broadcast', [
    //         //     'user_id' => $order->user_id,
    //         //     'order_id' => $order->id,
    //         //     'status' => $order->status
    //         // ]);

    //         return response()->json(['status'=>true, 'message'=>"Successfully the order's status has been updated"]);
    //     }catch(Exception $e){
    //         return response()->json(['status'=>false, 'code'=>$e->getCode(), 'message'=>$e->getMessage()],500);
    //     }
    // }

    public function orderStatusUpdate(Request $request)
    {
        try {
            $order = Orderdetail::findOrFail($request->order_id);
            $order->status = $request->status;
            $order->save();

            $user = User::findorfail($order->user_id);

            //Node.js Socket Server URL
            $url = "https://theclays.shop/order-status-change";

            //Call socket server
            Http::post($url, [
                'user_id' => $order->user_id,
                'order_id' => $order->id,
                'status' => $order->status
            ]);


            $fcmData = [
                'success' => "true",
                'message' => 'Successfully Updated',
                'order_id' => strval($order->id),
                'user_id' => strval($order->user_id),
                'status' => strval($order->status)
            ];



                $serviceAccount = json_decode(file_get_contents(public_path('fcm/the-clays-bd-31e5bad2a3c6.json')), true);

                $now = time();
                $jwt = JWT::encode([
                    'iss' => $serviceAccount['client_email'],
                    'scope' => 'https://www.googleapis.com/auth/firebase.messaging',
                    'aud' => 'https://oauth2.googleapis.com/token',
                    'iat' => $now,
                    'exp' => $now + 3600
                ], $serviceAccount['private_key'], 'RS256');

                // Exchange JWT for access token
                $ch = curl_init('https://oauth2.googleapis.com/token');
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/x-www-form-urlencoded']);
                curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query([
                    'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
                    'assertion' => $jwt
                ]));
                $pushResponse = curl_exec($ch);
                curl_close($ch);

                $data = json_decode($pushResponse, true);
                if (!isset($data['access_token'])) {
                    return response()->json([
                        'status' => false,
                        'message' => 'Failed to get access token',
                        'error' => $response
                    ], 500);

                }

                $accessToken = $data['access_token'];

                //return $accessToken;

                // FCM endpoint
                $fcmUrl = 'https://fcm.googleapis.com/v1/projects/' . $serviceAccount['project_id'] . '/messages:send';

                // Build payload
                $payload = [
                    'message' => [
                        'token' => $user->device_token,
                        //'token' => "feVa4f55QIOE1E7z5WuuCG:APA91bHyPOmnkvTEqaHD5lX7H7szdnW0cqRwoywxHZsNFkTJnPbFqkvgSjYfTZLEABa_LPZG7WdQX7eE2D3fQY5CTGdKpeURYOcpM7MwEDmsd1_48WAUkRg",
                        'notification' => [
                            'title' => "Order Status Update!",
                            'body' => "Your current Order's is $order->status"
                        ],
                        //'data' => $request->extra_data ?? []
                        'data' => $fcmData
                    ]
                ];

                // Send notification
                $ch = curl_init($fcmUrl);
                curl_setopt($ch, CURLOPT_HTTPHEADER, [
                    'Authorization: Bearer ' . $accessToken,
                    'Content-Type: application/json'
                ]);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));

                $fcmResponse = curl_exec($ch);
                $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                curl_close($ch);
                //return $fcmResponse;
                return response()->json([
                    'status' => true,
                    'message' => "Successfully updated order status & notified user"
                ]);

        } catch(Exception $e) {
            return response()->json([
                'status'=>false,
                'message'=>$e->getMessage()
            ], 500);
        }
    }
    
    public function userStatusUpdate(Request $request)
    {
        try
        {
            $user = User::findorfail($request->user_id);
            $user->status = $request->status;
            $user->update();
            return response()->json(['status'=>true, 'user_id'=>intval($user->id), 'message'=>"Successfully the user's status has been updated"]);
        }catch(Exception $e) {
            return response()->json([
                'status'=>false,
                'message'=>$e->getMessage()
            ], 500);
        }
    }

}
