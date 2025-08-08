<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ShopifyController;

Route::get('/', function () {
    return view('welcome');
});


Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
    ])->group(function () {
        Route::get('/shopify/products', [ShopifyController::class, 'index'])->name('shopify.products.index');
        Route::get('/shopify/orders', [ShopifyController::class, 'orders_view'])->name('shopify.orders.index');
    });

