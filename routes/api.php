<?php

use App\Http\Controllers\abortController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\OrderItemController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\WarehouseController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// *---- Shared Routes ----*
Route::group([
    'middleware'=>['auth:api']
],function(){
    Route::get('logout',[UserController::class,'destroy']);

    Route::patch('user/update',[UserController::class,'update']);

    Route::get('user',[UserController::class,'show']);

    Route::post('notify',[NotificationController::class,'create']);

    Route::patch('order/{order_id}/update',[OrderController::class,'update']);
});

Route::post('register',[UserController::class,'create']);

Route::post('login',[UserController::class,'store']);

// *---- End shared Routes ----*


// *---- App Routes ----*
Route::group([
    'middleware'=>['auth:api']
],function(){
    Route::get('products',[ProductController::class,'index']);
    Route::get('product/{product_id}',[ProductController::class,'show']);

    //orders
    Route::get('orders',[OrderController::class,'index']);
    Route::get('order/{order_id}',[OrderController::class,'show']);
    Route::post('order/create',[OrderController::class,'create']);
    Route::delete('order/{order_id}/delete',[OrderController::class,'destroy']);
    Route::delete('orderItem/{order_item_id}/delete',[OrderItemController::class,'destroy']);

    //all warehouses
    Route::get('warehouses',[WarehouseController::class,'index']);

    Route::get('warehouse/{warehouse_id}',[WarehouseController::class,'show']);

    //all categories
    Route::get('categories',[CategoryController::class,'index']);

    //favorites
    Route::get('favorites',[FavoriteController::class,'index']);
    Route::get('favorite/{order_id}',[FavoriteController::class,'show']);
    Route::post('favorite/product/{product_id}/create',[FavoriteController::class,'create']);
    Route::delete('favorite/{favorites_id}/delete',[FavoriteController::class,'destroy']);
});
// *---- End app Routes ----*


// *---- Web Routes ----*
Route::group([
    'middleware'=>['auth:api']
],function(){
    //products
    Route::post('product/create',[ProductController::class,'create']);
    Route::patch('product/{product_id}/update',[ProductController::class,'update']);
    Route::delete('product/{product_id}/delete',[ProductController::class,'destroy']);

});
// *---- End web Routes ----*

