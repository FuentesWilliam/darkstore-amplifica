<?php

namespace App\Http\Controllers;

use App\Services\ShopifyService;

class ShopifyController extends Controller
{
    protected $shopify;

    public function __construct(ShopifyService $shopify)
    {
        $this->shopify = $shopify;
    }

    public function index()
    {
        // Sincronizar y obtener productos
        $this->shopify->syncAllProducts();
       
        return view('shopify.products');
    }

    public function orders_view()
    {
        // Sincronizar y obtener productos
        $this->shopify->syncAllOrders();
       
        return view('shopify.orders');
    }
}
