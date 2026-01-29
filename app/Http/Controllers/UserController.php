<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DataTables;
use App\Models\User;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth_check');
    }

    public function users(Request $request)
    {
        if ($request->ajax()) {

            $user = User::where('role','user')->latest();

            return Datatables::of($user)
                ->addIndexColumn()

                ->addColumn('status', function ($row) {
                    $isActive = $row->status === 'Active';

                    return '
                        <label class="switch">
                            <input 
                                type="checkbox"
                                id="status-user-update"
                                class="' . ($isActive ? 'active-user' : 'decline-user') . '"
                                data-id="' . $row->id . '"
                                ' . ($isActive ? 'checked' : '') . '
                            >
                            <span class="slider round"></span>
                        </label>
                    ';
                })

                ->addColumn('action', function ($row) {


                    return '

                        <a href="#" 
                           class="btn btn-danger btn-sm delete-user action-button" 
                           data-id="' . $row->id . '">
                            <i class="fa fa-trash"></i>
                        </a>
                    ';
                })

                ->rawColumns(['status', 'action'])
                ->make(true);
        }

        return view('users.index');
    }

    public function deleteUser($id)
    {
    	try
    	{
    		$user = User::findorfail($id);
    		$user->delete();
    	}catch(Exception $e){
            return response()->json(['status'=>false, 'code'=>$e->getCode(), 'message'=>$e->getMessage()],500);
        }
    }
}
