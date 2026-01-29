<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\IndexController;
use App\Http\Controllers\AccessController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\UnitController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\RatingController;
use App\Http\Controllers\SettingController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/status-change-socket',function(){
	return view('status_change_socket');
});

Route::get('/', [IndexController::class, 'loginPage']);

Route::post('admin-login', [AccessController::class, 'adminLogin']);

Route::get('/logout', [AccessController::class, 'Logout']);


Route::group(['middleware' => 'prevent-back-history'],function(){
  
  //admin dashboard

    Route::get('/dashboard', [DashboardController::class, 'Dashboard']);

  //categories
    Route::resource('categories', CategoryController::class);

  //units
    Route::resource('units', UnitController::class);
  //items
    Route::resource('items', ItemController::class);

   //orders
    Route::get('/order-lists', [OrderController::class, 'orderLists'])->name('order.lists');
    Route::get('/order-details/{id}', [OrderController::class, 'orderDetails']);
    Route::get('/delete-order/{id}', [OrderController::class, 'orderDelete']);

    //restaurant
    Route::get('/restaurant-info', [OrderController::class, 'restaurantInfo']);
    Route::post('set-restaurant-info', [OrderController::class, 'setRestaurantInfo']);
    
    //users
    Route::get('/users', [UserController::class, 'users']);
    Route::get('/delete-user/{id}', [UserController::class, 'deleteUser']);

    //ratings
    Route::get('/ratings', [RatingController::class, 'ratings']);
    Route::get('/delete-rating/{id}', [RatingController::class, 'deleteRating']);
    

    //settings
    Route::get('/change-password', [SettingController::class, 'changePassword']);

    Route::post('password-change', [SettingController::class, 'passwordChange']);

});