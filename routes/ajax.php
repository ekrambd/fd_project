<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AjaxController;

Route::post('category-status-update', [AjaxController::class, 'categoryStatusUpdate']);
Route::post('unit-status-update', [AjaxController::class, 'unitStatusUpdate']);
Route::post('item-status-update', [AjaxController::class, 'itemStatusUpdate']);
Route::post('/order-status-update', [AjaxController::class, 'orderStatusUpdate']);
Route::post('user-status-update', [AjaxController::class, 'userStatusUpdate']);