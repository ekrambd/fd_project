@extends('admin_master')
@section('content')

<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">All Orders</h1>
                </div><!-- /.col -->
                <div class="col-sm-6 text-right">
                    <!-- 🔊 ENABLE SOUND BUTTON -->
                    <button id="enableSound" class="btn btn-success btn-sm">
                        🔊 Enable Order Sound
                    </button>
                </div><!-- /.col -->
            </div><!-- /.row -->
        </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->
    <section class="content">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">All Orders</h3>
            </div>
            <!-- /.card-header -->
            <div class="card-body">
            	<div class="card w-100">
                  <div class="card-header">
                    <h5>Filter Order</h5>
                  </div>

                  <div class="card-body">
                     <div class="row">
                      <div class="col-md-4">
                        <div class="form-group">
                          <label for="from_date">From Date</label>
                          <input type="date" class="form-control" id="from_date"/>
                        </div>
                        
                      </div> 


                      <div class="col-md-4">
                        <div class="form-group">
                          <label for="to_date">To Date</label>
                          <input type="date" class="form-control" id="to_date"/>
                        </div>
                        
                      </div>

                      <div class="col-md-4">
                      	<div class="form-group">
                      	  <label for="status">Status</label>
                      	  <select class="form-control select2bs4" id="status">
                      	  	<option value="" selected="" disabled="">Select Status</option>
                      	  	<option value='pending'>Pending</option>
          					        <option value='confirm'>Confirm</option>
          					        <option value='cancel'>Cancel</option>
          					        <option value='processing'>Processing</option>
          					        <option value='prepared'>Prepared</option>
          					        <option value='delivered'>Delivered</option>
                      	  </select>	
                      	</div>
                      </div>

                      <div class="col-md-12 d-flex justify-content-center button-product-filters">

                        <button type="button" class="btn btn-primary filter-order"><i class="fa fa-search"></i> SEARCH</button>

                        <button type="button" class="btn btn-danger reset-filter">RESET</button>




                     </div>

                     </div>
                  </div>
                </div>

                <div class="fetch-data table-responsive">
                    <table id="order-tbl-table" class="table table-bordered table-striped data-table">
                        <thead>
                            <tr>
                            	<th>Order No</th>
                                <th>Customer Name</th>
                                <th>Customer Phone</th>
                                <th>Order Type</th>
                                <th>Order Date/Time</th>
                                <th>Total (BDT)</th>
                                <th>Status</th>
                                <th>Arrival Status</th>
                                <th>Disctance (km)</th>
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

<audio id="myAudio">
    <source src="{{asset('audio/notification.mp3')}}" type="audio/mpeg">
</audio>
@endsection

@push('scripts')
  
  <script>
  	$(document).ready(function(){
  		let order_id;

      const audio = $('#myAudio')[0];
      let soundEnabled = localStorage.getItem('soundEnabled') === '1';

      // আগের session থেকে sound enabled হলে button disable
      if(soundEnabled){
          //$('#enableSound').prop('disabled', true).text('🔊 Sound Enabled');
          audio.muted = false;
          audio.volume = 1;
      }

      // Enable sound button click
      $('#enableSound').on('click', function(){
          audio.muted = false;
          audio.volume = 1;
          audio.play().then(() => {
              audio.pause();
              audio.currentTime = 0;
              soundEnabled = true;
              localStorage.setItem('soundEnabled','1');
              $('#enableSound').prop('disabled', true).text('🔊 Sound Enabled');
              toastr.success('🔊 Order sound enabled');
          }).catch(err=>{
              console.error('Audio play failed', err);
              toastr.error('Please click again to enable sound');
          });
      });

      // function to play notification
      function playNotificationSound(){
          if(soundEnabled){
              audio.currentTime = 0;
              audio.play().catch(err => console.error(err));
          }
      }

  		

  		var orderTable = $('#order-tbl-table').DataTable({
		        searching: true,
		        processing: true,
		        serverSide: true,
		        ordering: false,
		        responsive: true,
		        stateSave: true,
		        ajax: {
		          url: "{{route('order.lists')}}",
		          data: function (d) {

		                d.from_date = $('#from_date').val(),
		                d.to_date = $('#to_date').val(),
		                d.status = $('#status').val()
		                d.search = $('.dataTables_filter input').val()
	               }
		        },

		        columns: [
		            {data: 'order_no', name: 'order_no'},
		            {data: 'customer_name', name: 'customer_name'},
		            {data: 'customer_phone', name: 'customer_phone'},
		            {data: 'order_type', name: 'order_type'},
		            {data: 'order_datetime', name: 'order_datetime'},
		            {data: 'total', name: 'total'},
		            {data: 'status', name: 'status'},
		            {data: 'user_arrival_status', name: 'user_arrival_status'},
                {data: 'distance', name: 'distance'},
		            {data: 'action', name: 'action', orderable: false, searchable: false},
		        ]
        });

  		$('.filter-order').click(function(e){
	        e.preventDefault();
	        orderTable.draw(); 
	    });

	     // ======================
    // SOCKET.IO LISTENING
    // ======================
      

      // Listen new orders in real-time
      // socket.on("new_order_received", function(data){
      //     //console.log("New order received:", data);


      //     $('.data-table').DataTable().ajax.reload(null, false);

      //     // Optional: Show toast
      //     toastr.success(`New Order #${data.data.order_no} received`);

      //     //alert(countOrder);

      //     $('#order-count').text(countOrder);
      // });

      // socket.on("new_order_received", function(data){

      //     // ✅ Increase count
      //     countOrder++;

      //     // ✅ Update badge
      //     $('#order-count').text(countOrder);

      //     // ✅ Reload table
      //     $('.data-table').DataTable().ajax.reload(null, false);

      //     playNotificationSound();

      //     // ✅ Toast
      //     toastr.success(`New Order #${data.data.order_no} received`);
      // });



      // socket.on("user_arrival_status_updated", function(payload){
      //     console.log("User arrival updated:", payload);

      //     // Reload DataTable
      //     //orderTable.ajax.reload(null, false);

      //     $('.data-table').DataTable().ajax.reload(null, false);

      //     // Optional: Show toast
      //     toastr.info(payload.message);
      // });



      const socket = io("https://theclays.shop"); // Node server URL
    let Admin_ID = "{{ user()->id }}";   // Logged in admin ID

    // Join admin room
    socket.emit("join_admin_room", Admin_ID);

    // Listeners
    socket.on("new_order_received", function(data){
        countOrder++;
        $('#order-count').text(countOrder);
        $('.data-table').DataTable().ajax.reload(null, false);
        playNotificationSound();
        toastr.success(`New Order #${data.data.order_no} received`);
    });

    socket.on("user_arrival_status_updated", function(payload){
        console.log("User arrival updated:", payload);
        $('.data-table').DataTable().ajax.reload(null, false);
        toastr.info(payload.message);
    });

    socket.on("user_location_update", function(payload){
        console.log("User location update received:", payload);
        $('.data-table').DataTable().ajax.reload(null, false);
        toastr.info(`Order #${payload.order_id} distance updated: ${payload.distance} km`);
    });


       $(document).on('change', '.order-status-change', function(){
       		if(confirm('Do you want to chanege the status?'))
       		{
	   			order_id = $(this).data('id');
		        var status_val = $(this).val(); 
		        $.ajax({

	                url: "{{url('/order-status-update')}}",

	                     type:"POST",
	                     data:{'order_id':order_id, 'status':status_val},
	                     dataType:"json",
	                     success:function(data) {

	                        toastr.success(data.message);

	                        $('.data-table').DataTable().ajax.reload(null, false);

	                },
		                            
		        });
       		}
	         
       }); 


       $(document).on('click', '.delete-order', function(e){

           e.preventDefault();

           order_id = $(this).data('id');

           if(confirm('Do you want to delete this?'))
           {
               $.ajax({

                    url: "{{url('/delete-order/')}}/"+order_id,

                         type:"GET",
                         dataType:"json",
                         success:function(data) {

                            toastr.success(data.message);

                            $('.data-table').DataTable().ajax.reload(null, false);

                            window.location.reload();

                    },
                                
              });
           }

       });

  	});
  </script>

@endpush