@extends('admin_master')
@section('content')

<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">All Rating</h1>
                </div><!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{URL::to('/dashboard')}}">Dashboard</a></li>
                        <li class="breadcrumb-item active">All Rating</li>
                    </ol>
                </div><!-- /.col -->
            </div><!-- /.row -->
        </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->
    <section class="content">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">All Rating</h3>
            </div>
            <!-- /.card-header -->
            <div class="card-body">

                <div class="fetch-data table-responsive">
                    <table id="rating-table" class="table table-bordered table-striped data-table">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Phone</th>
                                <th>Rate</th>
                                <th>Comment</th>
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
  		let rating_id;
  		var rateTable = $('#rating-table').DataTable({
		        searching: true,
		        processing: true,
		        serverSide: true,
		        ordering: false,
		        responsive: true,
		        stateSave: true,
		        ajax: {
		          url: "{{url('/ratings')}}",
		        },

		        columns: [
		            {data: 'user_name', name: 'user_name'},
		            {data: 'user_phone', name: 'user_phone'},
                {data: 'rate', name: 'rate'},
                {data: 'comment', name: 'comment'},
		            {data: 'action', name: 'action', orderable: false, searchable: false},
		        ]
        });




       $(document).on('click', '.delete-rating', function(e){

           e.preventDefault();

           rating_id = $(this).data('id');

           if(confirm('Do you want to delete this?'))
           {
               $.ajax({

                    url: "{{url('/delete-rating')}}/"+rating_id,

                         type:"GET",
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