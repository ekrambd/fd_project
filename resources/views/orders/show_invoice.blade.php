@extends('admin_master')
@section('content')

<div class="content-wrapper" id="printarea">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1>Invoice</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="#">Home</a></li>
              <li class="breadcrumb-item active">Invoice</li>
            </ol>
          </div>
        </div>
      </div>
    </section>

    <section class="content">
      <div class="container-fluid">
        <div class="row">
          <div class="col-12">

            <!-- Main content -->
            <div class="invoice p-3 mb-3">
              <!-- title row -->
              <div class="row">
                <div class="col-12">
                  <h4>
                    <i class="fas fa-globe"></i> {{ restaurant()->restaurant_name }}
                    <small class="float-right">Date: {{ $data->date }}</small>
                  </h4>
                </div>
              </div>

              <!-- info row -->
              <div class="row invoice-info">
                <div class="col-sm-4 invoice-col">
                  From
                  <address>
                    <strong>{{ $data->customer_name }}</strong><br>
                    <strong>Order Type: {{ $data->order_type }}</strong><br>
                    Phone: {{ $data->customer_phone }}<br>
                    Email: {{ $data->customer_email }}
                  </address>
                </div>

                <div class="col-sm-4 invoice-col"></div>

                <div class="col-sm-4 invoice-col">
                  <b>Invoice #{{ $data->order_no }}</b><br>
                  <b>Payment Method: {{ $data->paymentmethod->name }}</b><br>
                  <br>
                  <b>Order ID:</b> {{ $data->id }}<br>
                  <b>Order Status:</b> {{ $data->status }}<br>
                </div>
              </div>

              <!-- Table row -->
              <div class="row">
                <div class="col-12 table-responsive">
                  <table class="table table-striped">
                    <thead>
                      <tr>
                        <th>SL</th>
                        <th>Item</th>
                        <th>Price</th>
                        <th>Qty</th>
                        <th>Unit</th>
                        <th>Unit Total</th>
                      </tr>
                    </thead>
                    <tbody>
                      @foreach($data->orders as $key => $row)
                        <tr>
                          <td>{{ $key + 1 }}</td>
                          <td>{{ $row->item->item_name }}</td>
                          <td>{{ $row->item_price }}</td>
                          <td>{{ $row->qty }}</td>
                          <td>{{ $row->item->unit->unit_name }}</td>
                          <td>{{ $row->unit_total }} BDT</td>
                        </tr>
                      @endforeach
                    </tbody>
                  </table>
                </div>
              </div>

              <!-- Order summary -->
              <div class="row">
                <div class="col-6"></div>
                <div class="col-6">
                  <p class="lead font-weight-bold">Order Summary</p>
                  <div class="table-responsive">
                    <table class="table table-bordered">
                      <tr>
                        <th>Total:</th>
                        <td>{{ $data->total }} BDT</td>
                      </tr>
                    </table>
                  </div>
                </div>
              </div>

            </div> <!-- /.invoice -->
          </div>
        </div>
        <div class="text-center float-left">
        <button id="printBtn" class="btn btn-primary"><i class="fa fa-print"></i> Print Invoice</button>
      </div>
      </div>
      
    </section>
</div>



@endsection

@push('scripts')
<script>
document.getElementById('printBtn').addEventListener('click', function() {
    // Print area content
    var printContents = document.getElementById('printarea').innerHTML;

    // Copy current page head (CSS & JS)
    var headContent = document.head.innerHTML;

    // Open new tab
    var newWin = window.open('', '_blank', 'width=1200,height=800');

    // Write full HTML
    newWin.document.open();
    newWin.document.write(`
        <html>
            <head>
                ${headContent}
                <style>
                    body { margin: 20px; }
                    .invoice { margin: 0; }
                    @media print {
                        body { -webkit-print-color-adjust: exact; }
                        .btn { display: none; }
                    }
                </style>
            </head>
            <body onload="window.print(); setTimeout(function(){ window.close(); }, 500);">
                ${printContents}
            </body>
        </html>
    `);
    newWin.document.close();
});
</script>
@endpush
