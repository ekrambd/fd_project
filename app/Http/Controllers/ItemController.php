<?php

namespace App\Http\Controllers;

use App\Models\Item;
use Illuminate\Http\Request;
use App\Http\Requests\StoreItemRequest;
use App\Http\Requests\UpdateItemRequest;
use DataTables;

class ItemController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function __construct()
    {
        $this->middleware('auth_check');
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {

            $items = Item::latest();

            return Datatables::of($items)
                ->addIndexColumn()

                ->addColumn('category', function ($row) {
                    return $row->category->category_name;
                })

                ->addColumn('unit', function ($row) {
                    return $row->unit->unit_name;
                })

                ->addColumn('status', function ($row) {
                    $isActive = $row->status === 'Active';

                    return '
                        <label class="switch">
                            <input 
                                type="checkbox"
                                id="status-item-update"
                                class="' . ($isActive ? 'active-item' : 'decline-item') . '"
                                data-id="' . $row->id . '"
                                ' . ($isActive ? 'checked' : '') . '
                            >
                            <span class="slider round"></span>
                        </label>
                    ';
                })

                ->addColumn('action', function ($row) {

                    $editUrl = route('items.show', $row->id);

                    return '
                        <a href="' . $editUrl . '" 
                           class="btn btn-primary btn-sm action-button edit-item" 
                           data-id="' . $row->id . '">
                            <i class="fa fa-edit"></i>
                        </a>

                        <a href="#" 
                           class="btn btn-danger btn-sm delete-item action-button" 
                           data-id="' . $row->id . '">
                            <i class="fa fa-trash"></i>
                        </a>
                    ';
                })

                ->rawColumns(['status', 'category', 'unit', 'action'])
                ->make(true);
        }

        return view('items.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('items.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreItemRequest $request)
    {
        try
        {   
            if ($request->file('image')) {
                $file = $request->file('image');
                $name = time() . user()->id . $file->getClientOriginalName();
                $file->move(public_path() . '/uploads/items/', $name);
                $path = 'uploads/items/' . $name;
            }

            $item = new Item();
            $item->user_id = user()->id;
            $item->category_id = $request->category_id;
            $item->unit_id = $request->unit_id;
            $item->item_name = $request->item_name;
            $item->item_price = $request->item_price;
            $item->item_discount = $request->item_discount;
            $item->description = $request->description;
            $item->making_duration = $request->making_duration;
            $item->making_duration_unit = $request->making_duration_unit;
            $item->image = $path;
            $item->status = $request->status;
            $item->save();
            $notification=array(
                'messege'=>"Successfully an item has been added",
                'alert-type'=>"success",
            );

            return redirect()->back()->with($notification); 
        }catch(Exception $e){
            return response()->json(['status'=>false, 'code'=>$e->getCode(), 'message'=>$e->getMessage()],500);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Item  $item
     * @return \Illuminate\Http\Response
     */
    public function show(Item $item)
    {
        return view('items.edit', compact('item'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Item  $item
     * @return \Illuminate\Http\Response
     */
    public function edit(Item $item)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Item  $item
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateItemRequest $request, Item $item)
    {
        try
        {   
            if ($request->file('image')) {
                $file = $request->file('image');
                $name = time() . user()->id . $file->getClientOriginalName();
                $file->move(public_path() . '/uploads/items/', $name);
                unlink(public_path($item->image));
                $path = 'uploads/items/' . $name;
            }else{
                $path = $item->image;
            }

            $item->category_id = $request->category_id;
            $item->unit_id = $request->unit_id;
            $item->item_name = $request->item_name;
            $item->item_price = $request->item_price;
            $item->item_discount = $request->item_discount;
            $item->description = $request->description;
            $item->making_duration = $request->making_duration;
            $item->making_duration_unit = $request->making_duration_unit;
            $item->image = $path;
            $item->status = $request->status;
            $item->update();

            $notification=array(
                'messege'=>"Successfully the item has been updated",
                'alert-type'=>"success",
            );

            return redirect('/items')->with($notification); 

        }catch(Exception $e){
            return response()->json(['status'=>false, 'code'=>$e->getCode(), 'message'=>$e->getMessage()],500);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Item  $item
     * @return \Illuminate\Http\Response
     */
    public function destroy(Item $item)
    {
        try
        {
            unlink(public_path($item->image));
            $item->delete();
            return response()->json(['status'=>true, 'message'=>"Successfully the item has been deleted"]);
        }catch(Exception $e){
            return response()->json(['status'=>false, 'code'=>$e->getCode(), 'message'=>$e->getMessage()],500);
        }
    }
}
