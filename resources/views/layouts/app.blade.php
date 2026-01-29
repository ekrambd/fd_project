@php
use App\Models\User;
use App\Models\Orderdetail;
use App\Models\Category;
use App\Models\Item;
use Illuminate\Support\Facades\DB;

// একবারে সব হিসাব নেয়া
$stats = [
    'total_users' => User::where('role','user')->count(),
    'total_pending_orders' => Orderdetail::where('status','pending')->count(),
    'total_today_orders' => Orderdetail::whereDate('date', now())->sum('total'),
    'total_this_month_orders' => Orderdetail::whereMonth('date', now()->month)->sum('total'),
    'total_categories' => Category::count(),
    'total_items' => Item::count(),
    'today_pending_orders' => Orderdetail::where('status','pending')->whereDate('date', now())->count(),
    'this_month_pending_orders' => Orderdetail::where('status','pending')->whereMonth('date', now()->month)->count(),
];
@endphp

@extends('admin_master')
@section('content')
<div class="content-wrapper">
    <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1>Dashboard</h1>
          </div>
        </div>
      </div>
    </section>

    <section class="content">
      <div class="container-fluid">
        <div class="row">

          <div class="col-lg-3 col-6">
            <div class="small-box bg-info">
              <div class="inner">
                <h3>{{ $stats['total_pending_orders'] }}</h3>
                <p>Pending Orders</p>
              </div>
              <div class="icon d-none">
                <i class="ion ion-bag"></i>
              </div>
              <a href="{{ url('/order-lists') }}" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
            </div>
          </div>

          <div class="col-lg-3 col-6">
            <div class="small-box bg-success">
              <div class="inner">
                <h3>{{ round($stats['total_today_orders'],2) }} BDT</h3>
                <p>Today's Orders Total</p>
              </div>
              <div class="icon d-none">
                <i class="ion ion-stats-bars"></i>
              </div>
              <a href="{{ url('/order-lists') }}" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
            </div>
          </div>

          <div class="col-lg-3 col-6">
            <div class="small-box bg-warning">
              <div class="inner">
                <h3>{{ round($stats['total_this_month_orders'],2) }} BDT</h3>
                <p>This Month Orders Total</p>
              </div>
              <div class="icon d-none">
                <i class="ion ion-person-add"></i>
              </div>
              <a href="{{ url('/order-lists') }}" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
            </div>
          </div>

          <div class="col-lg-3 col-6">
            <div class="small-box bg-danger">
              <div class="inner">
                <h3>{{ $stats['total_users'] }}</h3>
                <p>Total Users</p>
              </div>
              <div class="icon d-none">
                <i class="ion ion-pie-graph"></i>
              </div>
              <a href="#" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
            </div>
          </div>

        </div>

        <div class="row">
          <div class="col-lg-3 col-6">
            <div class="small-box bg-info">
              <div class="inner">
                <h3>{{ $stats['total_categories'] }}</h3>
                <p>Total Categories</p>
              </div>
              <div class="icon d-none"><i class="ion ion-pricetag"></i></div>
              <a href="{{ url('categories') }}" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
            </div>
          </div>

          <div class="col-lg-3 col-6">
            <div class="small-box bg-success">
              <div class="inner">
                <h3>{{ $stats['total_items'] }}</h3>
                <p>Total Items</p>
              </div>
              <div class="icon d-none"><i class="ion ion-ios-cart"></i></div>
              <a href="{{ url('items') }}" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
            </div>
          </div>

          <div class="col-lg-3 col-6">
            <div class="small-box bg-warning">
              <div class="inner">
                <h3>{{ $stats['today_pending_orders'] }}</h3>
                <p>Today's Pending Orders</p>
              </div>
              <div class="icon d-none"><i class="ion ion-clock"></i></div>
              <a href="{{ url('/order-lists') }}" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
            </div>
          </div>

          <div class="col-lg-3 col-6">
            <div class="small-box bg-danger">
              <div class="inner">
                <h3>{{ $stats['this_month_pending_orders'] }}</h3>
                <p>This Month Pending Orders</p>
              </div>
              <div class="icon d-none"><i class="ion ion-alert"></i></div>
              <a href="{{ url('/order-lists') }}" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
            </div>
          </div>
        </div>

      </div>
    </section>
</div>
@endsection
