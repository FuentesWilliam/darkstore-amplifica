<?php

use Illuminate\Support\Facades\Route;
use app\Http\Controllers\ShopifyController;

Route::get('/', function () {
    return view('welcome');
});
Route::get('/shopify/products', [ShopifyController::class, 'index'])->name('shopify.products.index');

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
    ])->group(function () {
        Route::get('/dashboard', function () {
            return view('dashboard');
        })->name('dashboard');
    });

