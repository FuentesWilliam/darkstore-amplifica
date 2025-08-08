<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use App\Models\Product;

class ShopifyService
{
    protected string $shopDomain;
    protected string $accessToken;

    public function __construct()
    {
        $this->shopDomain = env('SHOPIFY_SHOP_DOMAIN');
        $this->accessToken = env('SHOPIFY_ACCESS_TOKEN');
    }

    public function getProducts()
    {
        $response = Http::withHeaders([
            'X-Shopify-Access-Token' => $this->accessToken,
        ])->get("https://{$this->shopDomain}/admin/api/2024-01/products.json", [
            'limit' => 250,
            'fields' => 'id,title,variants,images'
        ]);

        $data = $response->json();

        return collect($data['products'])->map(function($product) {
            return $this->syncProduct($product);
        });
    }

    protected function syncProduct(array $productData): Product
    {
        $variant = $productData['variants'][0] ?? [];
        
        return Product::updateOrCreate(
            ['id' => $productData['id']],
            [
                'title' => $productData['title'],
                'sku' => $variant['sku'] ?? null,
                'price' => $variant['price'] ?? 0,
                'image_url' => $productData['images'][0]['src'] ?? null,
                'variants' => $productData['variants'],
                'images' => $productData['images']
            ]
        );
    }

    public function syncAllProducts()
    {
        $products = $this->getProducts();
        return $products->count();
    }
}
