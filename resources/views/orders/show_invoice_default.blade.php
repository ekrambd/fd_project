<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin | Dashboard</title>

    <style>
        body {
            font-family: 'Courier New', monospace;
            font-size: 14px;
        }

        #invoice-POS{
            box-shadow: 0 0 1in -0.25in rgba(0, 0, 0, 0.5);
            padding:2mm;
            margin: 0 auto;
            width: 44mm;
            background: #FFF;


            ::selection {background: #f31544; color: #FFF;}
            ::moz-selection {background: #f31544; color: #FFF;}
            h1{
                font-size: 1.5em;
                color: #222;
            }
            h2{font-size: .9em;}
            h3{
                font-size: 1.2em;
                font-weight: 300;
                line-height: 2em;
            }
            p{
                font-size: .7em;
                color: #666;
                line-height: 1.2em;
            }

            #top, #mid,#bot{ /* Targets all id with 'col-' */
                border-bottom: 1px solid #EEE;
            }

            #top{min-height: 100px;}
            #mid{min-height: 80px;}
            #bot{ min-height: 50px;}

            #top .logo{
            //float: left;
                height: 60px;
                width: 60px;
                background: url('{{ asset($order->domain->logo) }}') no-repeat;
                background-size: 60px 60px;
            }
            .clientlogo{
                float: left;
                height: 60px;
                width: 60px;
                background: url("{{ config('app.url') }}/{{ $order->domain->logo }}") no-repeat;
                background-size: 60px 60px;
                border-radius: 50px;
            }
            .info{
                display: block;
            //float:left;
                margin-left: 0;
            }
            .title{
                float: right;
            }
            .title p{text-align: right;}
            table{
                width: 100%;
                border-collapse: collapse;
            }
            td{
            //padding: 5px 0 5px 15px;
            //border: 1px solid #EEE
            }
            .tabletitle{
            //padding: 5px;
                font-size: .5em;
                background: #EEE;
            }
            .service{border-bottom: 1px solid #EEE;}
            .item{width: 24mm;}
            .itemtext{font-size: .5em;}

            #legalcopy{
                margin-top: 5mm;
            }



        }
    </style>
</head>
<body>

<div id="invoice-POS">
    <center id="top">
        <div class="logo"></div>
        <div class="info">
            <h2>{{ $order->domain->shop_name ?? "" }}</h2>
            <h5>
                Order ID : {{ $OrderID ?? '' }}</br>
                Merchant ID : {{ $settings->merchant_id ?? '' }}</br>
            </h5>
        </div><!--End Info-->
    </center><!--End InvoiceTop-->

    <div id="mid">
        <div class="info">
            <h2>{{ $order->customer_name ?? "" }}</h2>
            <h5>
                Address : {{ $order->customer_address ?? "" }}</br>
                Phone   : {{ $order->customer_phone ?? "" }}</br>
            </h5>
        </div>
    </div><!--End Invoice Mid-->

    <div id="bot">

        <div id="table">
            <table>
                {{--
                <tr class="tabletitle">
                    <td class="item"><h2>Item</h2></td>
                    <td class="Hours"><h2>Qty</h2></td>
                    <td class="Rate"><h2>Sub Total</h2></td>
                </tr>

                @if(count($order->orders) > 0)
                    @foreach($order->orders as $key=>$item)
                        <tr class="service">
                            <td class="tableitem">
                                <p class="itemtext">
                                    {{ $item->product->product_name ?? 'N/A' }}
                                    @if($item->variant_id)
                                        <br>
                                        ({{ $item->variant->variant_name ?? '' }} : {{ $item->variant->variant_value ?? '' }})
                                    @endif
                                </p>
                            </td>
                            <td class="tableitem"><p class="itemtext">{{$item->qty}}</p></td>
                            <td class="tableitem"><p class="itemtext">{{$item->unit_total}} BDT</p></td>
                        </tr>
                    @endforeach
                    @else
                    <p>Nothing found.</p>
                @endif



                <tr class="tabletitle">
                    <td></td>
                    <td class="Rate"><h2>Delivery Charge</h2></td>
                    @if($order->delivery_charge != NULL)
                    <td class="payment"><h2>{{ $order->delivery_charge ?? 0 }} BDT</h2></td>
                    @else
                        <td>Free</td>
                    @endif
                </tr>
                --}}

                <tr class="" style="border: 1px solid #000;">
                    <td></td>
                    <td class="Rate"><h2>Total</h2></td>
                    <td class="payment"><h2>{{ $order->total }} BDT</h2></td>
                </tr>

            </table>
        </div><!--End Table-->

    </div><!--End InvoiceBot-->
</div><!--End Invoice-->

</body>

<script>
    window.onload = function() {
        window.print();
    }
</script>

</html>
