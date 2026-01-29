<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DataTables;
use App\Models\Rating;

class RatingController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth_check');
    }

    public function ratings(Request $request)
    {
        if ($request->ajax()) {

            $ratings = Rating::latest();

            return Datatables::of($ratings)
                ->addIndexColumn()

                ->addColumn('user_name', function ($row) {


                    return $row->user->name;
                })

                ->addColumn('user_phone', function ($row) {


                    return $row->user->phone;
                })


                ->addColumn('action', function ($row) {


                    return '

                        <a href="#" 
                           class="btn btn-danger btn-sm delete-rating action-button" 
                           data-id="' . $row->id . '">
                            <i class="fa fa-trash"></i>
                        </a>
                    ';
                })

                ->rawColumns(['action', 'user_name', 'user_phone'])
                ->make(true);
        }

        return view('ratings.index');
    }

    public function deleteRating($id)
    {
    	try
    	{
    		$rate = Rating::findorfail($id);
    		$rate->delete();
    		return response()->json(['status'=>true, 'message'=>"Successfully the rate has been deleted"]);
    	}catch(Exception $e){
            return response()->json(['status'=>false, 'code'=>$e->getCode(), 'message'=>$e->getMessage()],500);
        }
    }
}
