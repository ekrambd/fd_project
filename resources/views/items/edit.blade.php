@extends('admin_master')
@section('content')
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Edit Item</h1>
                </div><!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{URL::to('/dashboard')}}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{URL::to('/items')}}">All Item
                                </a></li>
                        <li class="breadcrumb-item active">Edit Item</li>
                    </ol>
                </div><!-- /.col -->
            </div><!-- /.row -->
        </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->

    <section class="content">
        <div class="card card-success">
            <div class="card-header">
                <h3 class="card-title">Edit Item</h3>
            </div>
            <!-- /.card-header -->
            <!-- form start -->
            <form action="{{route('items.update',$item->id)}}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PATCH')
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="item_name">Item Name <span class="required">*</span></label>
                                <input type="text" name="item_name" class="form-control" id="item_name"
                                    placeholder="Item Name" required="" value="{{old('item_name',$item->item_name)}}">
                                @error('item_name')
                                <span class="alert alert-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div> 

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="unit_id">Select Unit <span class="required">*</span></label>
                                <select class="form-control select2bs4" name="unit_id" id="unit_id" required="">
                                    <option value="" selected="" disabled="">Select Unit</option>
                                    @foreach(units() as $unit)
                                     <option value="{{$unit->id}}" <?php if($unit->id == $item->unit_id){echo "selected";} ?>>{{$unit->unit_name}}</option>
                                    @endforeach
                                </select>
                                @error('unit_id')
                                <span class="alert alert-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>


                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="category_id">Select Category <span class="required">*</span></label>
                                <select class="form-control select2bs4" name="category_id" id="category_id" required="">
                                    <option value="" selected="" disabled="">Select Category</option>
                                    @foreach(categories() as $category)
                                     <option value="{{$category->id}}" <?php if($category->id == $item->category_id){echo "selected";} ?>>{{$category->category_name}}</option>
                                    @endforeach
                                </select>
                                @error('category_id')
                                <span class="alert alert-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="item_price">Item Price <span class="required">*</span></label>
                                <input type="text" name="item_price" class="form-control numericInput" id="item_price"
                                    placeholder="Item Price" required="" value="{{old('item_price',$item->item_price)}}">
                                @error('item_price')
                                <span class="alert alert-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="item_discount">Item Discount (%) <span class="required">*</span></label>
                                <input type="text" name="item_discount" class="form-control numericInput" id="item_discount"
                                    placeholder="Item Discount" value="{{old('item_discount',$item->item_discount)}}">
                                @error('item_discount')
                                <span class="alert alert-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>


                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="making_duration">Making Duration <span class="required">*</span></label>
                                <input type="text" name="making_duration" class="form-control numericInput" id="making_duration"
                                    placeholder="Making Duration" value="{{old('making_duration',$item->making_duration)}}">
                                @error('making_duration')
                                <span class="alert alert-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="making_duration_unit">Making Duration Unit <span class="required">*</span></label>
                                <select class="form-control select2bs4" name="making_duration_unit" id="making_duration_unit" required="">
                                    <option value="" selected="" disabled="">Choose Option</option>
                                    <option value="Minutes" <?php if($item->making_duration_unit == 'Minutes'){echo "selected";} ?>>Minutes</option>
                                    <option value="Hour" <?php if($item->making_duration_unit == 'Hour'){echo "selected";} ?>>Hour</option>
                                </select>
                                @error('making_duration_unit')
                                <span class="alert alert-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="status">Select Status <span class="required">*</span></label>
                                <select class="form-control select2bs4" name="status" id="status" required="">
                                    <option value="" selected="" disabled="">Select Status</option>
                                    <option value="Active" <?php if($unit->status == 'Active'){echo "selected";} ?>>Active</option>
                                    <option value="Inactive" <?php if($unit->status == 'Inactive'){echo "selected";} ?>>Inactive</option>
                                </select>
                                @error('status')
                                <span class="alert alert-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-12">
                          <div class="form-group">
                            <label for="description">Description</label>
                            <textarea name="description" id="description">{!!old('description',$item->description)!!}</textarea>
                            @error('description')
                            <span class="alert alert-danger">{{ $message }}</span>
                            @enderror
                          </div>
                        </div>

                        <div class="col-md-12">
                          <div class="form-group">
                            <label for="image">Image <span class="required">*</span></label>
                            <input name="image" type="file" id="image" accept="image/*" class="dropify" data-height="150" data-default-file="{{URL::to($item->image)}}" />
                            @error('image')
                            <span class="alert alert-danger">{{ $message }}</span>
                            @enderror
                          </div>
                        </div>

                        
                        <div class="form-group w-100 px-2">
                            <button type="submit" class="btn btn-success">Save Changes</button>
                        </div>
                    </div>
                    <!-- /.card-body -->
                </div>
            </form>
        </div>
    </section>
</div>
@endsection