<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ApiController;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware(['throttle:60,1'])->group(function () {
    Route::post('user-signup', [ApiController::class, 'userSignup']);
    Route::post('user-signin', [ApiController::class, 'userSignin']);
    Route::middleware('auth:sanctum')->group( function () { 
      Route::post('user-signout', [ApiController::class, 'userSignOut']);
      //mobile app api's
      Route::get('/categories', [ApiController::class, 'categories']);
      Route::post('items', [ApiController::class, 'items']);
      Route::get('/category-details/{id}', [ApiController::class, 'categoryDetails']);
      Route::get('/item-details/{id}', [ApiController::class, 'itemDetails']);
      Route::post('set-device-token', [ApiController::class, 'setDeviceToken']);
      Route::post('set-lat-lon', [ApiController::class, 'setLatLon']);
      Route::post('save-order', [ApiController::class, 'saveOrder']);
      Route::post('order-logs', [ApiController::class, 'orderLogs']);
      Route::get('/order-details/{id}', [ApiController::class, 'orderDetails']);
      Route::get('/payment-methods', [ApiController::class, 'paymentMethods']);
      Route::post('user-arrival-change', [ApiController::class, 'userArrivalChange']);
      Route::post('check-distance', [ApiController::class, 'checkDistance']);
      Route::post('save-rate', [ApiController::class, 'saveRate']);
      Route::post('/rate-logs', [ApiController::class, 'rateLogs']);
      Route::post('change-password', [ApiController::class, 'changePassword']);
      Route::get('user-details', [ApiController::class, 'userDetails']);
      Route::post('user-profile-update', [ApiController::class, 'userProfileUpdate']);
    });
    Route::post('check-user-order', [ApiController::class, 'checkUserOrder']);
    Route::post('check-parking-distance', [ApiController::class, 'checkParkingDistance']);
    Route::post('order-status-change', [ApiController::class, 'orderStatusChange']);
    Route::post('send-otp', [ApiController::class, 'sendOTP']);
    Route::post('verify-otp', [ApiController::class, 'verifyOTP']);
    Route::post('user-password-update', [ApiController::class, 'userPasswordUpdate']);
    Route::post('/user-location-update', [ApiController::class, 'userLocationUpdate']);
});