<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DataTables;
use App\Models\Orderdetail;
use App\Models\Order;
use App\Models\Restaurant;

class OrderController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth_check');
    }

    // public function orderLists(Request $request)
    // {
    // 	if ($request->ajax()) {

    //         $orders = Orderdetail::latest();

    //         return Datatables::of($orders)
    //             ->addIndexColumn()

    // //             ->addColumn('status', function ($row) {

				// //     $status = $row->status;

				// //     $html = "<select class='order-status-change' data-id='".$row->id."'>
				// //         <option value='pending' ".($status == 'pending' ? 'selected' : '').">Pending</option>
				// //         <option value='confirm' ".($status == 'confirm' ? 'selected' : '').">Confirm</option>
				// //         <option value='cancel' ".($status == 'cancel' ? 'selected' : '').">Cancel</option>
				// //         <option value='processing' ".($status == 'processing' ? 'selected' : '').">Processing</option>
				// //         <option value='prepared' ".($status == 'prepared' ? 'selected' : '').">Prepared</option>
				// //         <option value='delivered' ".($status == 'delivered' ? 'selected' : '').">Delivered</option>
				// //     </select>";

				// //     return $html;
				// // })
				
				
				// ->addColumn('status', function ($row) {

    //                 $currentStatus = $row->status;
                
    //                 // Status flow order
    //                 $statuses = [
    //                     'pending',
    //                     'confirm',
    //                     'processing',
    //                     'prepared',
    //                     'delivered',
    //                     'cancel'
    //                 ];
                
    //                 $html = "<select class='order-status-change' data-id='".$row->id."'>";
                
    //                 foreach ($statuses as $status) {
                
    //                     $selected = $currentStatus === $status ? 'selected' : '';
                
    //                     // ðŸ”´ If order is NOT cancelled
    //                     if ($currentStatus !== 'cancel') {
                
    //                         // index compare
    //                         $currentIndex = array_search($currentStatus, $statuses);
    //                         $optionIndex  = array_search($status, $statuses);
                
    //                         // ðŸ”’ Disable previous statuses
    //                         $disabled = $optionIndex < $currentIndex ? 'disabled' : '';
    //                     } 
    //                     // ðŸŸ¢ If cancelled â†’ everything enabled
    //                     else {
    //                         $disabled = '';
    //                     }
                
    //                     $html .= "<option value='{$status}' {$selected} {$disabled}>"
    //                           . ucfirst($status) .
    //                           "</option>";
    //                 }
                
    //                 $html .= "</select>";
                
    //                 return $html;
    //             })



    //             ->addColumn('user_arrival_status', function ($row) {
    //                 return $row->user_arrival_status == NULL?"-":$row->user_arrival_status;
    //             })

    //             ->addColumn('distance', function($row){
    //                 return $row->distance_km ?? "-";
    //             })

    //             ->addColumn('order_type', function ($row) {
    //                 if($row->order_type == 'preorder')
    //                 {   
    //                 	$origDate = $row->preorder_date;
    //                     $newDate = date("d M Y", strtotime($origDate));

    //                 	$html = "<div><p>".ucfirst($row->order_type)."</p><p>".$row->preorder."</p><p>Date: ".$newDate."</p><p>Time: ".$row->preorder_time."</p></div>";
    //                 }else{
    //                 	$str = str_replace("_", " ", $row->order_type);
    //                 	$html = ucfirst($str);
    //                 }
    //                 return $html;
    //             })

    //             ->addColumn('order_datetime', function ($row) {
    //                 $origDate = $row->date;
    //                 $newDate = date("d M Y", strtotime($origDate));
    //                 return "<p>Date: ".$newDate."<p><p>Time: ".$row->time."<p>";
    //             })

    //             ->addColumn('action', function ($row) {

    //                 $viewUrl = url('/order-details/'.$row->id);

    //                 return '
    //                     <a href="' . $viewUrl . '" 
    //                       class="btn btn-primary btn-sm action-button view-order" 
    //                       data-id="' . $row->id . '">
    //                         <i class="fa fa-eye"></i>
    //                     </a>

    //                     <a href="#" 
    //                       class="btn btn-danger btn-sm delete-order action-button" 
    //                       data-id="' . $row->id . '">
    //                         <i class="fa fa-trash"></i>
    //                     </a>
    //                 ';
    //             })->filter(function ($instance) use ($request) {

    //                     if ($request->get('search') != "") {
    //                          $instance->where(function($w) use($request){
    //                             $search = $request->get('search');
    //                             $w->orWhere('orderdetails.customer_name', 'LIKE', "%$search%")->orWhere('orderdetails.customer_phone', 'LIKE', "%$search%")->orWhere('orderdetails.order_no', 'LIKE', "%$search%");
    //                         });
    //                     }

    //                     if ($request->get('from_date') != "") {
    //                          $instance->where(function($w) use($request){
    //                             $from_date = $request->get('from_date');
    //                             $w->orWhere('orderdetails.date', '>=', $request->from_date);
    //                         });
    //                     }

    //                     if ($request->get('to_date') != "") {
    //                          $instance->where(function($w) use($request){
    //                             $to_date = $request->get('to_date');
    //                             $w->orWhere('orderdetails.date', '<=', $request->to_date);
    //                         });
    //                     }

    //                     if ($request->get('status') != "") {
    //                          $instance->where(function($w) use($request){
    //                             $status = $request->get('status');
    //                             $w->orWhere('orderdetails.status', $status);
    //                         });
    //                     }

                            
    //                 })->setRowId(function ($row) {
				//         return 'order_'.$row->id;
				//     })

    //             ->rawColumns(['status', 'action', 'order_type', 'order_datetime','distance'])
    //             ->make(true);
    //     }

    //     return view('orders.index');

    // }
    
    
    public function orderLists(Request $request)
    {
        if ($request->ajax()) {
    
            $orders = Orderdetail::latest();
    
            return Datatables::of($orders)
                ->addIndexColumn()
    
                ->addColumn('status', function ($row) {
    
                    $currentStatus = $row->status;
    
                    $statuses = [
                        'pending'    => 'Pending',
                        'processing' => 'Confirm',   // 👈 confirm label, processing value
                        'prepared'   => 'Prepared',
                        'delivered'  => 'Delivered',
                        'cancel'     => 'Cancel',
                    ];
    
                    $html = "<select class='order-status-change' data-id='".$row->id."'>";
    
                    foreach ($statuses as $value => $label) {
    
                        $selected = $currentStatus === $value ? 'selected' : '';
    
                        if ($currentStatus !== 'cancel') {
    
                            $keys = array_keys($statuses);
                            $currentIndex = array_search($currentStatus, $keys);
                            $optionIndex  = array_search($value, $keys);
    
                            $disabled = $optionIndex < $currentIndex ? 'disabled' : '';
                        } else {
                            $disabled = '';
                        }
    
                        $html .= "<option value='{$value}' {$selected} {$disabled}>{$label}</option>";
                    }
    
                    $html .= "</select>";
    
                    return $html;
                })
    
                ->addColumn('user_arrival_status', function ($row) {
                    return $row->user_arrival_status == NULL ? "-" : $row->user_arrival_status;
                })
    
                ->addColumn('distance', function ($row) {
                    return $row->distance_km ?? "-";
                })
    
                ->addColumn('order_type', function ($row) {
                    if ($row->order_type == 'preorder') {
                        $newDate = date("d M Y", strtotime($row->preorder_date));
    
                        return "<div>
                            <p>".ucfirst($row->order_type)."</p>
                            <p>".$row->preorder."</p>
                            <p>Date: ".$newDate."</p>
                            <p>Time: ".$row->preorder_time."</p>
                        </div>";
                    }
    
                    return ucfirst(str_replace("_", " ", $row->order_type));
                })
    
                ->addColumn('order_datetime', function ($row) {
                    return "<p>Date: ".date("d M Y", strtotime($row->date))."</p>
                            <p>Time: ".$row->time."</p>";
                })
    
                ->addColumn('action', function ($row) {
    
                    $viewUrl = url('/order-details/'.$row->id);
    
                    return '
                        <a href="'.$viewUrl.'" class="btn btn-primary btn-sm">
                            <i class="fa fa-eye"></i>
                        </a>
                        <a href="#" class="btn btn-danger btn-sm delete-order" data-id="'.$row->id.'">
                            <i class="fa fa-trash"></i>
                        </a>
                    ';
                })
    
                ->filter(function ($instance) use ($request) {
    
                    if ($request->search) {
                        $instance->where(function ($w) use ($request) {
                            $w->where('orderdetails.customer_name', 'LIKE', "%{$request->search}%")
                              ->orWhere('orderdetails.customer_phone', 'LIKE', "%{$request->search}%")
                              ->orWhere('orderdetails.order_no', 'LIKE', "%{$request->search}%");
                        });
                    }
    
                    if ($request->from_date) {
                        $instance->where('orderdetails.date', '>=', $request->from_date);
                    }
    
                    if ($request->to_date) {
                        $instance->where('orderdetails.date', '<=', $request->to_date);
                    }
    
                    if ($request->status) {
                        $instance->where('orderdetails.status', $request->status);
                    }
    
                })
    
                ->setRowId(fn($row) => 'order_'.$row->id)
    
                ->rawColumns(['status','action','order_type','order_datetime','distance'])
    
                ->make(true);
        }
    
        return view('orders.index');
    }



    public function orderDetails($id)
    {   
        setReadNotify($id);
        // $notification = auth()->user()->notifications()->where('order_id', $id)->first();
        // if($notification) {
        //     $notification->markAsRead();
        // }

        $data =  Orderdetail::with([
                'paymentmethod',
                'orders.item' => function($q) {
                    $q->with(['category', 'unit']);
                }
            ])->findorfail($id);
        return view('orders.show_invoice', compact('data'));
    }

    public function restaurantInfo()
    {
        $data = restaurant();
        return view('restaurants.show', compact('data'));
    }

    public function setRestaurantInfo(Request $request)
    {
        try
        {   
            $restaurant = restaurant();
            $restaurant->owner_name = $request->owner_name;
            $restaurant->restaurant_name = $request->restaurant_name;
            $restaurant->lat = $request->lat;
            $restaurant->lon = $request->lon;
            $restaurant->address = $request->address;
            $restaurant->save();
            $notification=array(
                'messege'=>"Successfully Updated",
                'alert-type'=>"success",
            );

            return redirect()->back()->with($notification);
        }catch(Exception $e){
            return response()->json(['status'=>false, 'code'=>$e->getCode(), 'message'=>$e->getMessage()],500);
        }
    }

    public function orderDelete($id)
    {
        try
        {
            //setReadNotify($id);
            $notification = auth()->user()->notifications()->where('order_id', $id)->first();
            if($notification) {
                $notification->markAsRead();
            }
            $order = Orderdetail::findOrFail($id);
            $order->orders()->delete();   // child delete
            $order->delete();             // âœ… main order delete
            return response()->json(['status'=>true, 'message'=>"Successfully the order has been deleted"]);
        }catch(Exception $e){
            return response()->json(['status'=>false, 'code'=>$e->getCode(), 'message'=>$e->getMessage()],500);
        } 
    }
}
