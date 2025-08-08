<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Collection;
use App\Models\Product;
use App\Models\Order;

class ShopifyService
{
    protected string $shopDomain;
    protected string $accessToken;
    protected string $apiVersion = '2024-01';
    
    /**
     * Constructor de la clase ShopifyService.
     * Inicializa el dominio de la tienda y el token de acceso
     * utilizando las variables de entorno configuradas.
     */
    public function __construct()
    {
        $this->shopDomain = env('SHOPIFY_SHOP_DOMAIN');
        $this->accessToken = env('SHOPIFY_ACCESS_TOKEN');
    }

    /**
     * Obtiene productos de la tienda Shopify.
     * 
     * @return \Illuminate\Support\Collection Colección de productos mapeados
     */
    public function getProducts(array $params = []): Collection
    {
        $defaultParams = [
            'limit' => 250,
            'fields' => implode(',', [
                'id',
                'title',
                'sku',
                'price',
                'images',
                'variants'
            ])
        ];

        $finalParams = array_merge($defaultParams, $params);

        $response = Http::withHeaders([
            'X-Shopify-Access-Token' => $this->accessToken,
        ])->get("https://{$this->shopDomain}/admin/api/{$this->apiVersion}/products.json", $finalParams);

        if (!$response->successful()) {
            throw new \Exception("Error al obtener productos: " . $response->body());
        }

        return collect($response->json('products') ?? []);
    }

    /**
     * Obtiene pedidos de la tienda Shopify.
     * 
     * @param array $params Parámetros opcionales para la consulta:
     *        - limit: Número máximo de pedidos a recuperar (default: 250)
     *        - status: Estado de los pedidos (any, open, closed, cancelled)
     *        - created_at_min: Fecha mínima de creación
     *        - fields: Campos a incluir en la respuesta
     * @return \Illuminate\Support\Collection Colección de pedidos
     */
    public function getOrders(array $params = []): Collection
    {
        $defaultParams = [
            'limit' => 250,
            'status' => 'any',
            'fields' => implode(',', [
                'id',
                'order_number',
                'customer',
                'created_at',
                'financial_status',
                'line_items',
                'subtotal_price',
            ])
        ];

        $finalParams = array_merge($defaultParams, $params);

        $response = Http::withHeaders([
            'X-Shopify-Access-Token' => $this->accessToken,
        ])->get("https://{$this->shopDomain}/admin/api/{$this->apiVersion}/orders.json", $finalParams);

        if (!$response->successful()) {
            throw new \Exception("Error al obtener pedidos: " . $response->body());
        }

        return collect($response->json('orders') ?? []);
    }

    /**
     * Sincroniza los datos de un producto de Shopify con la base de datos local.
     * 
     * @param array $productData Datos del producto desde Shopify
     * @return Product Modelo del producto sincronizado
     */
    protected function syncProduct(array $productData): Product
    {
        $variant = $productData['variants'][0] ?? [];
        
        return Product::updateOrCreate(
            ['id' => $productData['id']],
            [
                'title' => $productData['title'],
                'sku' => $variant['sku'] ?? null,
                'price' => $variant['price'] ?? 0,
                'images' => $productData['images'],
                'variants' => $productData['variants'],
            ]
        );
    }

    /**
     * Sincroniza un pedido individual con la base de datos local.
     * 
     * @param array $orderData Datos del pedido desde Shopify
     * @return Order Modelo del pedido sincronizado
     */
    public function syncOrder(array $orderData): Order
    {
        // Crear o actualizar el pedido
        return Order::updateOrCreate(
            ['id' => $orderData['id']],
            [
                'order_number' => $orderData['order_number'],
                'customer' => $orderData['customer'] ?? null,
                'created_at' => $orderData['created_at'],
                'updated_at' => $orderData['updated_at'] ?? now(),
                'financial_status' => $orderData['financial_status'],
                'line_items' => $orderData['line_items'] ?? [],
                'subtotal_price' => $orderData['subtotal_price']
            ]
        );
    }

    /**
     * Sincroniza todos los productos con la base de datos local.
     * 
     * @return int Cantidad de productos sincronizados
     */
    public function syncAllProducts()
    {
        $products = $this->getProducts();

        $products->each(function ($productData) {
            $this->syncProduct($productData);
        });
        return true;
    }

    /**
     * Sincroniza todos los pedidos con la base de datos local.
     * 
     * @param array $params Parámetros opcionales para filtrar los pedidos
     * @return int Cantidad de pedidos sincronizados
     */
    public function syncAllOrders(array $params = [])
    {
        $orders = $this->getOrders($params);
        $orders->each(function ($orderData) {
            $this->syncOrder($orderData);
        });
        return true;
    }
}