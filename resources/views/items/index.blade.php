@extends('admin_master')
@section('content')

<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">All Item</h1>
                </div><!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{URL::to('/dashboard')}}">Dashboard</a></li>
                        <li class="breadcrumb-item active">All Item</li>
                    </ol>
                </div><!-- /.col -->
            </div><!-- /.row -->
        </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->
    <section class="content">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">All Item</h3>
            </div>
            <!-- /.card-header -->
            <div class="card-body">
                <a href="{{route('items.create')}}" class="btn btn-primary add-new mb-2">Add New Item</a>
                <div class="fetch-data table-responsive">
                    <table id="item-table" class="table table-bordered table-striped data-table">
                        <thead>
                            <tr>
                                <th>Item Name</th>
                                <th>Category</th>
                                <th>Unit</th>
                                <th>Price (BDT)</th>
                                <th>Discount (%)</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody class="conts"> 
                        </tbody>
                    </table> 
                </div>
            </div>
        </div>
    </section>
</div>
@endsection

@push('scripts')
  
  <script>
  	$(document).ready(function(){
  		let item_id;
  		var itemTable = $('#item-table').DataTable({
		        searching: true,
		        processing: true,
		        serverSide: true,
		        ordering: false,
		        responsive: true,
		        stateSave: true,
		        ajax: {
		          url: "{{url('/items')}}",
		        },

		        columns: [
		            {data: 'item_name', name: 'item_name'},
		            {data: 'category', name: 'category'},
		            {data: 'unit', name: 'unit'},
		            {data: 'item_price', name: 'item_price'},
		            {data: 'item_discount', name: 'item_discount'},
		            {data: 'status', name: 'status'},
		            {data: 'action', name: 'action', orderable: false, searchable: false},
		        ]
        });



       $(document).on('click', '#status-item-update', function(){

	         var item_id = $(this).data('id');
	         var isItemchecked = $(this).prop('checked');
	         var status_val = isItemchecked ? 'Active' : 'Inactive'; 
	         $.ajax({

                url: "{{url('/item-status-update')}}",

                     type:"POST",
                     data:{'item_id':item_id, 'status':status_val},
                     dataType:"json",
                     success:function(data) {

                        toastr.success(data.message);

                        $('.data-table').DataTable().ajax.reload(null, false);

                },
	                            
	        });
       }); 


       $(document).on('click', '.delete-item', function(e){

           e.preventDefault();

           item_id = $(this).data('id');

           if(confirm('Do you want to delete this?'))
           {
               $.ajax({

                    url: "{{url('/items')}}/"+item_id,

                         type:"DELETE",
                         dataType:"json",
                         success:function(data) {

                            toastr.success(data.message);

                            $('.data-table').DataTable().ajax.reload(null, false);

                    },
                                
              });
           }

       });

  	});
  </script>

@endpush