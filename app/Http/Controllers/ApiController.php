<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Category;
use App\Models\Item;
use App\Models\Orderdetail;
use App\Models\Order;
use App\Models\Restaurant;
use App\Models\Paymentmethod;
use App\Models\Rating;
use App\Notifications\OrderCreated;
use Illuminate\Support\Facades\Http;
use Auth;
use Validator;
use DB;
use Hash;
use Firebase\JWT\JWT;

class ApiController extends Controller
{
    public function userSignup(Request $request)
    {
        try
        {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:50',
                'phone' => 'required|string',
                'email' => 'nullable|email',
                'password' => 'required|string',
                'confirm_password' => 'required|string|same:password'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false, 
                    'message' => 'Please fill all requirement fields', 
                    'data' => $validator->errors()
                ], 422);  
            }

            $user = new User();

            if($request->has('email')){
                $countEmail = User::where('email',$request->email)->count();
                if($countEmail > 0){
                    return response()->json(['status'=>false, 'message'=>"Already the email has been taken", "data"=>new \stdClass()],400);
                }
            }

            if($request->has('phone')){
                $countPhone = User::where('phone',$request->phone)->count();
                if($countPhone > 0){
                    return response()->json(['status'=>false, 'message'=>"Already the phone has been taken", "data"=>new \stdClass()],400);
                }
            }

            $user = new User();
            $user->name = $request->name;
            $user->phone = $request->phone;
            $user->email = $request->email;
            $user->password = bcrypt($request->password);
            $user->save();

            return response()->json(['status'=>true, 'message'=>"Successfully Signup", "data"=>$user],201);

        }catch(Exception $e){
            return response()->json(['status'=>false, 'code'=>$e->getCode(), 'message'=>$e->getMessage()],500);
        }
    }

    public function userSignin(Request $request)
    {
        try
        {
            $validator = Validator::make($request->all(), [
                'login' => 'required|string',
                'password' => 'required|string',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false, 
                    'message' => 'Please fill all requirement fields', 
                    'data' => $validator->errors()
                ], 422);  
            }

            $login = $request->input('login');
            $password = $request->input('password');

            $fieldType = filter_var($login, FILTER_VALIDATE_EMAIL) ? 'email' : 'phone';

            $user = User::where('email',$login)->orWhere('phone',$login)->first();
            
            if($user->status == 'Inactive'){
                return response()->json(['status'=>false, 'message'=>'Sorry you are not active user', 'token'=>"", 'user'=>new \stdClass()],403);
            }

            if (Auth::attempt([$fieldType => $login, 'password' => $password])) {
                $token = $user->createToken('MyApp')->plainTextToken;
                return response()->json(['status'=>true,'message'=>'Successfully Logged IN', 'token'=>$token, 'user'=>$user]);
            }

            return response()->json(['status'=>false,'message'=>"Invalid Email/Phone or Password", 'token'=>"", 'user'=>new \stdClass()],401);

        }catch(Exception $e){
            return response()->json(['status'=>false, 'code'=>$e->getCode(), 'message'=>$e->getMessage()],500);
        }
    }

    public function userSignOut(Request $request)
    {
        try
        {
            auth()->user()->tokens()->delete();
            return response()->json(['status'=>true, 'message'=>'Successfully Logged Out']);
        }catch(Exception $e){
            return response()->json(['status'=>false, 'code'=>$e->getCode(), 'message'=>$e->getMessage()],500);
        }
    }

    public function categories()
    {
        try
        {
            $categories = Category::whereHas('items')->where('status','Active')->latest()->get();
            return response()->json(['status'=>count($categories) > 0, 'data'=>$categories]);
        }catch(Exception $e){
            return response()->json(['status'=>false, 'code'=>$e->getCode(), 'message'=>$e->getMessage()],500);
        }
    }

    public function items(Request $request)
    {
        try
        {
            //$items = Item::with('unit','category')->where('status','Active')->latest()->paginate(10);
            $query = Item::query();
            if($request->has('search'))
            {
                $search = $request->search;
                $query->where('item_name', 'LIKE', "%{$search}%");
            }

            if(isset($request->category_id))
            {
                $query->where('category_id',$request->category_id);
            }
            $items = $query->with('unit','category')->where('status','Active')->latest()->paginate(10);
            return response()->json($items);
        }catch(Exception $e){
            return response()->json(['status'=>false, 'code'=>$e->getCode(), 'message'=>$e->getMessage()],500);
        }
    }

    public function categoryDetails($id)
    {
        try
        {   

            $category = Category::findOrFail($id);

            $items = $category->items()->where('status', 'Active')->latest()->paginate(10);

            return response()->json([
                'id' => $category->id,
                'category_name' => $category->category_name,
                'status' => $category->status,
                'created_at' => $category->created_at,
                'updated_at' => $category->updated_at,
                'items' => $items,
            ]);

        }catch(Exception $e){
            return response()->json(['status'=>false, 'code'=>$e->getCode(), 'message'=>$e->getMessage()],500);
        }
    }

    public function itemDetails($id)
    {
        try
        {   

            $item = Item::with('category','unit')->findorfail($id);
            return response()->json(['status'=>true, 'data'=>$item]);

        }catch(Exception $e){
            return response()->json(['status'=>false, 'code'=>$e->getCode(), 'message'=>$e->getMessage()],500);
        }
    }

    public function setDeviceToken(Request $request)
    {
        try
        {
            $validator = Validator::make($request->all(), [
                'device_token' => 'required|string',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false, 
                    'message' => 'Please fill all requirement fields', 
                    'data' => $validator->errors()
                ], 422);  
            }

            $user = user();
            $user->device_token = $request->device_token;
            $user->update();

            return response()->json(['status'=>true, 'message'=>'Successfully updated']);

        }catch(Exception $e){
            return response()->json(['status'=>false, 'code'=>$e->getCode(), 'message'=>$e->getMessage()],500);
        }
    }

    public function setLatLon(Request $request)
    {
        try
        {
            $validator = Validator::make($request->all(), [
                'lat' => 'required|numeric',
                'lon' => 'required|numeric',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false, 
                    'message' => 'Please fill all requirement fields', 
                    'data' => $validator->errors()
                ], 422);  
            }

            $user = user();
            $user->lat = $request->lat;
            $user->lon = $request->lon;
            $user->update();

            return response()->json(['status'=>true, 'message'=>'Successfully updated']);

        }catch(Exception $e){
            return response()->json(['status'=>false, 'code'=>$e->getCode(), 'message'=>$e->getMessage()],500);
        }
    }

    public function saveOrder(Request $request)
    {   
        date_default_timezone_set('Asia/Dhaka');
        DB::beginTransaction();
        try
        {
            $validator = Validator::make($request->all(), [
                'paymentmethod_id' => 'required|integer|exists:paymentmethods,id',
                'driving_through_fee' => 'nullable|numeric',
                'order_type' => 'required|in:preorder,drive_through',
                'items' => 'required|array|min:1', 
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false, 
                    'message' => 'Please fill all requirement fields', 
                    'data' => $validator->errors()
                ], 422);  
            }

            $detail = new Orderdetail();
            $detail->user_id = user()->id;
            $detail->order_no = makeOrderNo();
            $detail->paymentmethod_id = $request->paymentmethod_id;
            $detail->customer_name = user()->name;
            $detail->customer_email = user()->email;
            $detail->customer_phone = user()->phone;
            $detail->date = date('Y-m-d');
            $detail->time = date('h:i:s a');
            $detail->timestamp = time();
            $detail->order_type = $request->order_type;
            $detail->preorder_date = $request->preorder_date;
            $detail->preorder_time = $request->preorder_time;
            $detail->lat = user()->lat;
            $detail->lon = user()->lon;
            $detail->status = 'pending';
            $detail->save();

            foreach($request->items as $row)
            {   
                $item = item($row['id']);
                $order = new Order();
                $order->orderdetail_id = $detail->id;
                $order->item_id = $item->id;
                $order->item_price = itemPrice($item->id);
                $order->qty = $row['qty'];
                $order->unit_total = itemPrice($item->id) * $row['qty'];
                $order->save();
            }

            $sum = Order::where('orderdetail_id',$detail->id)->sum('unit_total');

            Orderdetail::where('id',$detail->id)->update(['total'=>$sum]);

            $data = Orderdetail::with([
                'paymentmethod',
                'orders.item' => function($q) {
                    $q->with(['category', 'unit']);
                }
            ])->find($detail->id);


            Http::post('https://theclays.shop/order-save', [
                'admin_id' => 1,
                'status'   => true,
                'order_id' => intval($detail->id),
                'message'  => 'Successfully the order has been taken',
                'data'     => $data
            ]);


            $admin = User::where('role', 'admin')->first();

            $admin->notify(new OrderCreated($data));

            DB::table('notifications')
            ->where('id', $admin->notifications()->latest()->first()->id)
            ->update(['order_id' => $data->id]);


            DB::commit();

            return response()->json(['status'=>true, 'order_id'=>intval($detail->id), 'message'=>'Successfully the order has been taken', 'data'=>$data]);

        }catch(Exception $e){
            DB::rollback();
            return response()->json(['status'=>false, 'code'=>$e->getCode(), 'message'=>$e->getMessage()],500);
        }
    }

    public function orderLogs(Request $request)
    {
        try
        {
            $query = Orderdetail::query();
            if($request->has('order_type'))
            {
                $query->where('order_type',$request->order_type);
            }
            if($request->has('status'))
            {
                $query->where('status',$request->status);
            }
            if($request->has('from_date'))
            {
                $query->where('date','>=',$request->from_date);
            }
            if($request->has('to_date'))
            {
                $query->where('date','<=',$request->to_date);
            }
            $orders = Orderdetail::with([
                'paymentmethod',
                'orders.item' => function($q) {
                    $q->with(['category', 'unit']);
                }
            ])->where('user_id',user()->id)->latest()->paginate(10);
            return response()->json($orders);
        }catch(Exception $e){
            return response()->json(['status'=>false, 'code'=>$e->getCode(), 'message'=>$e->getMessage()],500);
        }
    }

    public function orderDetails($id)
    {
        try
        {
            $data = Orderdetail::with([
                'paymentmethod',
                'rating',
                'orders.item' => function($q) {
                    $q->with(['category', 'unit']);
                }
            ])->findorfail($id);
            return response()->json(['status'=>true, 'data'=>$data]);
        }catch(Exception $e){
            return response()->json(['status'=>false, 'code'=>$e->getCode(), 'message'=>$e->getMessage()],500);
        }
    }

    public function orderStatusChange(Request $request)
    {
        try
        {
            $validator = Validator::make($request->all(), [
                'order_id' => 'required|integer|exists:orderdetails,id',
                'status' => 'required|in:arrive_start,arrive_end'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false, 
                    'message' => 'Please fill all requirement fields', 
                    'data' => $validator->errors()
                ], 422);  
            }

            $order = Orderdetail::findorfail($request->order_id);
            $order->status = $request->status;
            $order->update();

            return response()->json(['status'=>true, 'message'=>"Successfully the order's status has been changed", 'data'=>$order]);

        }catch(Exception $e){
            return response()->json(['status'=>false, 'code'=>$e->getCode(), 'message'=>$e->getMessage()],500);
        }
    }

    // public function userArrivalChange(Request $request)
    // {
    //     try
    //     {
    //         $validator = Validator::make($request->all(), [
    //             'order_id' => 'required|integer|exists:orderdetails,id',
    //             'user_arrival_status' => 'required|in:arrive_start,arrive_end'
    //         ]);

    //         if ($validator->fails()) {
    //             return response()->json([
    //                 'status' => false, 
    //                 'message' => 'Please fill all requirement fields', 
    //                 'data' => $validator->errors()
    //             ], 422);  
    //         }

    //         $order = Orderdetail::findorfail($request->order_id);
    //         $order->user_arrival_status = $request->user_arrival_status;
    //         $order->update();

    //         return response()->json(['status'=>true, 'message'=>"Successfully the order's arrival status has been changed", 'data'=>$order]);

    //     }catch(Exception $e){
    //         return response()->json(['status'=>false, 'code'=>$e->getCode(), 'message'=>$e->getMessage()],500);
    //     }
    // }

    public function userArrivalChange(Request $request)
    {
        try
        {
            $validator = Validator::make($request->all(), [
                'order_id' => 'required|integer|exists:orderdetails,id',
                'user_arrival_status' => 'required|in:arrive_start,arrive_end'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false, 
                    'message' => 'Please fill all requirement fields', 
                    'data' => $validator->errors()
                ], 422);  
            }

            $order = Orderdetail::findOrFail($request->order_id);
            $order->user_arrival_status = $request->user_arrival_status;
            $order->update();

            // Prepare the payload
            $payload = [
                'status' => true,
                'message' => "Successfully the order's arrival status has been changed",
                'data' => $order
            ];

            Http::post('https://theclays.shop/user-arrival-change', [
                'admin_id' => 1,     
                'payload' => $payload
            ]);

            return response()->json($payload);

        } catch(Exception $e) {
            $errorPayload = [
                'status' => false,
                'code' => $e->getCode(),
                'message' => $e->getMessage()
            ];
            return response()->json($errorPayload, 500);
        }
    }


    public function paymentMethods(Request $request)
    {
        try
        {
            $data = Paymentmethod::latest()->get();
            return response()->json(['status'=>count($data) > 0, 'data'=>$data]);
        }catch(Exception $e){
            return response()->json(['status'=>false, 'code'=>$e->getCode(), 'message'=>$e->getMessage()],500);
        }
    }

    public function checkDistance(Request $request)
    {
        try
        {
            $validator = Validator::make($request->all(), [
                'user_lat' => 'required|numeric',
                'user_lon' => 'required|numeric',
                'order_type' => 'required|in:drive_through',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false, 
                    'message' => 'Please fill all requirement fields', 
                    'data' => $validator->errors()
                ], 422);  
            }

            $restaurant = restaurant();

            $lat1 = $restaurant->lat;
            $lon1 = $restaurant->lon;
            $lat2 = $request->user_lat;
            $lon2 = $request->user_lon;

            $earthRadius = 6371;

            $dLat = deg2rad($lat2 - $lat1);
            $dLon = deg2rad($lon2 - $lon1);

            $a = sin($dLat / 2) * sin($dLat / 2) +
                 cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
                 sin($dLon / 2) * sin($dLon / 2);

            $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

            $distance = round($earthRadius * $c,2); 

            if($distance > 10)
            {
                return response()->json(['status'=>false, 'distance'=>strval($distance), 'message'=>"No restaurant found within 10km", 'data'=>new \stdClass()],404);
            }

            return response()->json(['status'=>true, 'message'=>"Restaurant found", 'distance'=>strval($distance), 'data'=>$restaurant],200);

        }catch(Exception $e){
            return response()->json(['status'=>false, 'code'=>$e->getCode(), 'message'=>$e->getMessage()],500);
        }
    }

    public function saveRate(Request $request)
    {   
        date_default_timezone_set('Asia/Dhaka');
        try
        {
            $validator = Validator::make($request->all(), [
                'order_id' => 'required|integer|exists:orderdetails,id',
                'rate' => 'required|integer|min:1|max:5',
                'comment' => 'required',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false, 
                    'message' => 'Please fill all requirement fields', 
                    'data' => $validator->errors()
                ], 422);  
            }

            $rate = new Rating();
            $rate->user_id = user()->id;
            $rate->orderdetail_id = $request->order_id;
            $rate->rate = $request->rate;
            $rate->comment = $request->comment;
            $rate->date = date('Y-m-d');
            $rate->time = date('h:i a');
            $rate->save();
            return response()->json(['status'=>true, 'message'=>"Successfully a rating has been added",'data'=>$rate]);
        }catch(Exception $e){
            return response()->json(['status'=>false, 'code'=>$e->getCode(), 'message'=>$e->getMessage()],500);
        }
    }

    public function rateLogs(Request $request)
    {
        try 
        {   
            $query = Rating::query();
            if($request->has('order_id'))
            {
                $query->where('orderdetail_id',$request->order_id);
            }
            $data = $query->where('user_id',user()->id)->latest()->paginate(10);
            return response()->json($data);
        }catch(Exception $e){
            return response()->json(['status'=>false, 'code'=>$e->getCode(), 'message'=>$e->getMessage()],500);
        }
    }

    public function changePassword(Request $request)
    {
        try
        {
            $validator = Validator::make($request->all(), [
                'new_password' => 'required',
                'confirm_password' => 'required|same:new_password',
                'current_password' => 'required',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false, 
                    'message' => 'Please fill all requirement fields', 
                    'data' => $validator->errors()
                ], 422);  
            }

            $user = user();
            //$message = $user->changePassword($request,$user);

            if (!Hash::check($request->current_password, $user->password)) {
            
               return response()->json(['status'=>false, 'message'=>"The current password is incorrect"],400);
            } 

            $user->password = Hash::make($request->new_password);
            $user->update();

            return response()->json(['status'=>true, 'message'=>"Your password has been changed"],200);

        }catch(Exception $e){
            return response()->json(['status'=>false, 'code'=>$e->getCode(), 'message'=>$e->getMessage()],500);
        }
    }

    public function userDetails()
    {
        try
        {
            $user = user();
            return response()->json(['status'=>true, 'data'=>$user]);
        }catch(Exception $e){
            return response()->json(['status'=>false, 'code'=>$e->getCode(), 'message'=>$e->getMessage()],500);
        }
    }

    public function userProfileUpdate(Request $request)
    {
        try
        {   
            $user = user();
            $validator = Validator::make($request->all(), [
                'name' => 'required|string',
                'email' => 'nullable|email|unique:users,email,' . $user->id,
                'phone' => 'required|string|unique:users,phone,' . $user->id,
                'image' => 'nullable',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false, 
                    'message' => 'Please fill all requirement fields', 
                    'data' => $validator->errors()
                ], 422);  
            }

            if ($request->file('image')) {
                $file = $request->file('image');
                $name = time() . user()->id . $file->getClientOriginalName();
                $file->move(public_path() . '/uploads/items/', $name);
                if($user->image != 'defaults/profile.png')
                {
                    unlink(public_path($user->image));
                }
                $path = 'uploads/items/' . $name;
            }else{
                $path = $user->image;
            }

            $user->name = $request->name;
            $user->email = $request->email;
            $user->phone = $request->phone;
            $user->image = $path;
            $user->update();

            return response()->json(['status'=>true, "message"=>"Successfully the profile has been updated"]);

        }catch(Exception $e){
            return response()->json(['status'=>false, 'code'=>$e->getCode(), 'message'=>$e->getMessage()],500);
        }
    }

    public function sendOTP(Request $request)
    {
        try
        {
            $validator = Validator::make($request->all(), [
                'phone' => 'required|string',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false, 
                    'message' => 'Please fill all requirement fields', 
                    'data' => $validator->errors()
                ], 422);  
            }

            $user = User::where('phone',$request->phone)->first();
            if(!$user){
                return response()->json(['status'=>false, 'message'=>'Invalid User'],404);
            }
            return response()->json(['status'=>true, 'message'=>'Verification OTP has been sent'],200);
        }catch(Exception $e){
            return response()->json(['status'=>false, 'code'=>$e->getCode(), 'message'=>$e->getMessage()],500);
        }
    }


    public function verifyOTP(Request $request)
    {
        try
        {
            $validator = Validator::make($request->all(), [
                'phone' => 'required|string',
                'otp' => 'required|numeric',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false, 
                    'message' => 'Please fill all requirement fields', 
                    'data' => $validator->errors()
                ], 422);  
            }

            $user = User::where('phone',$request->phone)->first();
            if(!$user){
                return response()->json(['status'=>false, 'message'=>'Invalid User'],404);
            }

            if($request->otp == '1234')
            {
                return response()->json(['status'=>true, 'phone'=>$request->phone, 'message'=>'Successfully Verified'],200);
            }

            return response()->json(['status'=>false, 'phone'=>"", 'message'=>'Failed to verify the otp'],40);

        }catch(Exception $e){
            return response()->json(['status'=>false, 'code'=>$e->getCode(), 'message'=>$e->getMessage()],500);
        }
    }

    public function userPasswordUpdate(Request $request)
    {
        try
        {
            $validator = Validator::make($request->all(), [
                'new_password' => 'required|string',
                'confirm_password' => 'required|string|same:new_password',
                'phone' => 'required|string',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false, 
                    'message' => 'Please fill all requirement fields', 
                    'data' => $validator->errors()
                ], 422);  
            }


            $user = User::where('phone',$request->phone)->first();
            if(!$user){
                return response()->json(['status'=>false, 'message'=>'Invalid User'],404);
            }

            $user->password = bcrypt($request->new_password);
            $user->update();

            return response()->json(['status'=>true, 'message'=>"Successfully your password has been reset"]);

        }catch(Exception $e){
            return response()->json(['status'=>false, 'code'=>$e->getCode(), 'message'=>$e->getMessage()],500);
        }
    }

    // public function userLocationUpdate(Request $request)
    // {

    //     $validator = Validator::make($request->all(), [
    //         'order_id' => 'required|integer|exists:orderdetails,id',
    //         'restaurant_id' => 'required|integer|exists:restaurants,id',
    //         'admin_id' => 'required|integer',
    //         'user_lat' => 'required|numeric',
    //         'user_lon' => 'required|numeric',
    //     ]);

    //     if ($validator->fails()) {
    //         return response()->json([
    //             'status' => false, 
    //             'message' => 'Please fill all requirement fields', 
    //             'data' => $validator->errors()
    //         ], 422);  
    //     }

    //     $order = Orderdetail::findOrFail($request->order_id);
    //     $restaurant = Restaurant::findOrFail($request->restaurant_id);

    //     // Calculate distance in KM
    //     $distance = $this->calculateDistance(
    //         $request->user_lat,
    //         $request->user_lon,
    //         $restaurant->lat,
    //         $restaurant->lon
    //     );

    //     // Save distance in order (optional)
    //     $order->distance_km = $distance;
    //     $order->save();

    //     // Emit to Node.js
    //     $payload = [
    //         'order_id' => $order->id,
    //         'distance' => $distance,
    //         'user_lat' => $request->user_lat,
    //         'user_lon' => $request->user_lon,
    //         'restaurant_lat' => $restaurant->lat,
    //         'restaurant_lon' => $restaurant->lon,
    //     ];

    //     // Laravel -> Node.js Socket emit using HTTP request
    //     Http::post('https://theclays.shop/user-location-update', [
    //         'admin_id' => $request->admin_id,
    //         'payload' => $payload
    //     ]);

    //     return response()->json([
    //         'status' => true,
    //         'message' => 'User location updated successfully',
    //         'distance_km' => $distance
    //     ]);
    // }
    
    public function userLocationUpdate(Request $request)
    {
            try
            {
                $validator = Validator::make($request->all(), [
                'order_id' => 'required|integer|exists:orderdetails,id',
                'restaurant_id' => 'required|integer|exists:restaurants,id',
                'admin_id' => 'required|integer',
                'user_lat' => 'required|numeric',
                'user_lon' => 'required|numeric',
            ]);
        
            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Please fill all requirement fields',
                    'data' => $validator->errors()
                ], 422);  
            }
        
            $order = Orderdetail::findOrFail($request->order_id);
            $restaurant = Restaurant::findOrFail($request->restaurant_id);
        
            // Calculate distance in KM (EXISTING)
            $distance = $this->calculateDistance(
                $request->user_lat,
                $request->user_lon,
                $restaurant->lat,
                $restaurant->lon
            );
        
            // Save distance in order (EXISTING)
            $order->distance_km = $distance;
            $order->save();
        
            // ================= ADDED PARKING CHECK =================
            $parkingLat = 23.791281033252048;
            $parkingLon = 90.40121633721594;
        
            $earthRadius = 6371; // KM
        
            $dLat = deg2rad($request->user_lat - $parkingLat);
            $dLon = deg2rad($request->user_lon - $parkingLon);
        
            $a = sin($dLat / 2) * sin($dLat / 2) +
                 cos(deg2rad($parkingLat)) * cos(deg2rad($request->user_lat)) *
                 sin($dLon / 2) * sin($dLon / 2);
        
            $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
            $parkingDistance = $earthRadius * $c;
            // ========================================================
        
            // Emit to Node.js (EXISTING)
            $payload = [
                'order_id' => $order->id,
                'distance' => $distance,
                'user_lat' => $request->user_lat,
                'user_lon' => $request->user_lon,
                'restaurant_lat' => $restaurant->lat,
                'restaurant_lon' => $restaurant->lon,
            ];
        
            Http::post('https://theclays.shop/user-location-update', [
                'admin_id' => $request->admin_id,
                'payload' => $payload
            ]);
        
            // ================= RESPONSE UPDATED =================
            if ($parkingDistance <= 0.1) {
                $userLat = $request->user_lat;
                $userLon = $request->user_lon;
                // return response()->json([
                //     'status' => true,
                //     'notification' => true,
                //     'message' => 'User is inside parking zone',
                //     'distance_km' => round($parkingDistance, 4)
                // ]);
                
                /* ================= DISTANCE CALCULATION (KM) ================= */
                $earthRadius = 6371; // KM
    
                $dLat = deg2rad($userLat - $parkingLat);
                $dLon = deg2rad($userLon - $parkingLon);
    
                $a = sin($dLat / 2) * sin($dLat / 2) +
                     cos(deg2rad($parkingLat)) * cos(deg2rad($userLat)) *
                     sin($dLon / 2) * sin($dLon / 2);
    
                $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
                $distance = $earthRadius * $c;
                /* ============================================================ */
    
                // If user is outside 0.1 KM (100 meters)
                if ($distance > 0.1) {
                    return response()->json([
                        'status' => false,
                        'message' => 'You are outside parking zone',
                        'distance_km' => round($distance, 4)
                    ]);
                }
    
                // ================= SEND FIREBASE NOTIFICATION =================
                $user = User::find($order->user_id);
    
                $fcmData = [
                    'success' => "true",
                    'message' => 'Now you are in parking zone',
                ];
    
                $serviceAccount = json_decode(
                    file_get_contents(public_path('fcm/the-clays-bd-31e5bad2a3c6.json')),
                    true
                );
    
                $now = time();
                $jwt = JWT::encode([
                    'iss' => $serviceAccount['client_email'],
                    'scope' => 'https://www.googleapis.com/auth/firebase.messaging',
                    'aud' => 'https://oauth2.googleapis.com/token',
                    'iat' => $now,
                    'exp' => $now + 3600
                ], $serviceAccount['private_key'], 'RS256');
    
                // Get Access Token
                $ch = curl_init('https://oauth2.googleapis.com/token');
                curl_setopt_array($ch, [
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_POST => true,
                    CURLOPT_HTTPHEADER => ['Content-Type: application/x-www-form-urlencoded'],
                    CURLOPT_POSTFIELDS => http_build_query([
                        'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
                        'assertion' => $jwt
                    ])
                ]);
    
                $tokenResponse = curl_exec($ch);
                curl_close($ch);
    
                $tokenData = json_decode($tokenResponse, true);
                if (!isset($tokenData['access_token'])) {
                    return response()->json([
                        'status' => false,
                        'message' => 'Failed to get FCM access token'
                    ], 500);
                }
    
                $accessToken = $tokenData['access_token'];
    
                // FCM Payload
                $payload = [
                    'message' => [
                        'token' => $user->device_token,
                        'notification' => [
                            'title' => 'Parking Alert',
                            'body' => 'You have entered the parking zone'
                        ],
                        'data' => $fcmData
                    ]
                ];
    
                $fcmUrl = 'https://fcm.googleapis.com/v1/projects/' .
                          $serviceAccount['project_id'] . '/messages:send';
    
                $ch = curl_init($fcmUrl);
                curl_setopt_array($ch, [
                    CURLOPT_HTTPHEADER => [
                        'Authorization: Bearer ' . $accessToken,
                        'Content-Type: application/json'
                    ],
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_POST => true,
                    CURLOPT_POSTFIELDS => json_encode($payload)
                ]);
    
                curl_exec($ch);
                curl_close($ch);
            }
        
            return response()->json([
                'status' => true,
                //'notification' => false,
                'message' => 'User location updated successfully',
                'distance_km' => round($distance, 4)
            ]);
        }catch(Exception $e){
            return response()->json(['status'=>false, 'code'=>$e->getCode(), 'message'=>$e->getMessage()],500);
        }
    }

    // Haversine formula
    private function calculateDistance($lat1, $lon1, $lat2, $lon2)
    {
        $earthRadius = 6371; // km

        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);

        $a = sin($dLat/2) * sin($dLat/2) +
            cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
            sin($dLon/2) * sin($dLon/2);

        $c = 2 * atan2(sqrt($a), sqrt(1-$a));
        return round($earthRadius * $c, 2); // distance in km
    }
    
    
    public function checkUserOrder(Request $request)
    {
        try
        {   
            $validator = Validator::make($request->all(), [
                'user_id' => 'required|integer|exists:users,id',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false, 
                    'message' => 'Please fill all requirement fields', 
                    'data' => $validator->errors()
                ], 422);  
            }
            $count = Orderdetail::where('user_id',$request->user_id)->where('status','!=','cancel')->orWhere('status','!=','delivered')->count();
            // $order = Orderdetail::with([
            //     'paymentmethod',
            //     'rating',
            //     'orders.item' => function($q) {
            //         $q->with(['category', 'unit']);
            //     }
            // ])->where('user_id',$request->user_id)->where('status','!=','cancel')->orWhere('status','!=','delivered')->latest()->get();
            
            $order = Orderdetail::with([
                'paymentmethod',
                'rating',
                'orders.item' => function ($q) {
                    $q->with(['category', 'unit']);
                }
            ])
            ->where('user_id', $request->user_id)
            ->whereNotIn('status', ['cancel', 'delivered'])
            ->latest()
            ->get();


            if(count($order) > 0){
                return response()->json(['status'=>true, 'data'=>$order, 'count'=>strval(count($order))]);
            }
            return response()->json(['status'=>false, 'data'=>array(), 'count'=>strval(count($order))],404);
        }catch(Exception $e){
            return response()->json(['status'=>false, 'code'=>$e->getCode(), 'message'=>$e->getMessage()],500);
        }
    }
    
    public function checkParkingDistance(Request $request)
    {
        try {

            $validator = Validator::make($request->all(), [
                'user_id' => 'required|integer|exists:users,id',
                'lat' => 'required|numeric',
                'lon' => 'required|numeric',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Please fill all requirement fields',
                    'data' => $validator->errors()
                ], 422);
            }

            // Fixed Parking Location
            $parkingLat = "23.791281033252048";
            $parkingLon = "90.40121633721594";

            // User Location
            $userLat = $request->lat;
            $userLon = $request->lon;

            /* ================= DISTANCE CALCULATION (KM) ================= */
            $earthRadius = 6371; // KM

            $dLat = deg2rad($userLat - $parkingLat);
            $dLon = deg2rad($userLon - $parkingLon);

            $a = sin($dLat / 2) * sin($dLat / 2) +
                 cos(deg2rad($parkingLat)) * cos(deg2rad($userLat)) *
                 sin($dLon / 2) * sin($dLon / 2);

            $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
            $distance = $earthRadius * $c;
            /* ============================================================ */

            // If user is outside 0.1 KM (100 meters)
            if ($distance > 0.1) {
                return response()->json([
                    'status' => false,
                    'message' => 'You are outside parking zone',
                    'distance_km' => round($distance, 4)
                ]);
            }

            // ================= SEND FIREBASE NOTIFICATION =================
            $user = User::find($request->user_id);

            $fcmData = [
                'success' => "true",
                'message' => 'Now you are in parking zone',
            ];

            $serviceAccount = json_decode(
                file_get_contents(public_path('fcm/the-clays-bd-31e5bad2a3c6.json')),
                true
            );

            $now = time();
            $jwt = JWT::encode([
                'iss' => $serviceAccount['client_email'],
                'scope' => 'https://www.googleapis.com/auth/firebase.messaging',
                'aud' => 'https://oauth2.googleapis.com/token',
                'iat' => $now,
                'exp' => $now + 3600
            ], $serviceAccount['private_key'], 'RS256');

            // Get Access Token
            $ch = curl_init('https://oauth2.googleapis.com/token');
            curl_setopt_array($ch, [
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_POST => true,
                CURLOPT_HTTPHEADER => ['Content-Type: application/x-www-form-urlencoded'],
                CURLOPT_POSTFIELDS => http_build_query([
                    'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
                    'assertion' => $jwt
                ])
            ]);

            $tokenResponse = curl_exec($ch);
            curl_close($ch);

            $tokenData = json_decode($tokenResponse, true);
            if (!isset($tokenData['access_token'])) {
                return response()->json([
                    'status' => false,
                    'message' => 'Failed to get FCM access token'
                ], 500);
            }

            $accessToken = $tokenData['access_token'];

            // FCM Payload
            $payload = [
                'message' => [
                    'token' => $user->device_token,
                    'notification' => [
                        'title' => 'Parking Alert',
                        'body' => 'You have entered the parking zone'
                    ],
                    'data' => $fcmData
                ]
            ];

            $fcmUrl = 'https://fcm.googleapis.com/v1/projects/' .
                      $serviceAccount['project_id'] . '/messages:send';

            $ch = curl_init($fcmUrl);
            curl_setopt_array($ch, [
                CURLOPT_HTTPHEADER => [
                    'Authorization: Bearer ' . $accessToken,
                    'Content-Type: application/json'
                ],
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_POST => true,
                CURLOPT_POSTFIELDS => json_encode($payload)
            ]);

            curl_exec($ch);
            curl_close($ch);

            return response()->json([
                'status' => true,
                'message' => 'You are in parking zone',
                'distance_km' => round($distance, 4)
            ]);

        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
    
}
