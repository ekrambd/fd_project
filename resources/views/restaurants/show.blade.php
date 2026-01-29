@extends('admin_master')
@section('content')
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Set Restaurant</h1>
                </div><!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{URL::to('/dashboard')}}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{URL::to('/restaurant-info')}}">Set Restaurant
                                </a></li>
                        <li class="breadcrumb-item active">Set Restaurant</li>
                    </ol>
                </div><!-- /.col -->
            </div><!-- /.row -->
        </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->

    <section class="content">
        <div class="card card-success">
            <div class="card-header">
                <h3 class="card-title">Set Restaurant</h3>
            </div>
            <!-- /.card-header -->
            <!-- form start -->
            <form action="{{url('set-restaurant-info')}}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="owner_name">Owner Name <span class="required">*</span></label>
                                <input type="text" name="owner_name" class="form-control" id="owner_name"
                                    placeholder="Owner Name" value="{{old('owner_name',$data->owner_name)}}">
                                @error('owner_name')
                                <span class="alert alert-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div> 


                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="restaurant_name">Restaurant Name <span class="required">*</span></label>
                                <input type="text" name="restaurant_name" class="form-control" id="restaurant_name"
                                    placeholder="Restaurant Name" required="" value="{{old('restaurant_name',$data->restaurant_name)}}">
                                @error('restaurant_name')
                                <span class="alert alert-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="lat">Latitude <span class="required">*</span></label>
                                <input type="text" name="lat" class="form-control numericInput" id="lat"
                                    placeholder="Restaurant Name" required="" value="{{old('lat',$data->lat)}}">
                                @error('lat')
                                <span class="alert alert-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>


                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="lon">Longitude <span class="required">*</span></label>
                                <input type="text" name="lon" class="form-control numericInput" id="lon"
                                    placeholder="Longitude" required="" value="{{old('lon',$data->lon)}}">
                                @error('lon')
                                <span class="alert alert-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="address">Address <span class="required">*</span></label>
                                <input type="text" name="address" class="form-control" id="address"
                                    placeholder="Address" required="" value="{{old('address',$data->address)}}">
                                @error('address')
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