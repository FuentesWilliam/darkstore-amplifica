<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use App\Models\Product;
use App\Models\Order;

class ShopifyService
{
    protected string $shopDomain;
    protected string $accessToken;
    
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
                'image_url' => $productData['images'][0]['src'] ?? null,
                'variants' => $productData['variants'],
                'images' => $productData['images']
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
        return $products->count();
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
    public function getOrders(array $params = [])
    {
        // Parámetros por defecto para la consulta de pedidos
        $defaultParams = [
            'limit' => 250,
            'status' => 'any', // Puedes cambiar a 'open', 'closed', 'cancelled' etc.
            'fields' => 'id,order_number,created_at,financial_status,fulfillment_status,total_price,line_items,customer'
        ];

        // Combinar parámetros por defecto con los proporcionados
        $finalParams = array_merge($defaultParams, $params);

        $response = Http::withHeaders([
            'X-Shopify-Access-Token' => $this->accessToken,
        ])->get("https://{$this->shopDomain}/admin/api/2024-01/orders.json", $finalParams);

        // Verificar si la respuesta fue exitosa
        if (!$response->successful()) {
            throw new \Exception("Error al obtener pedidos de Shopify: " . $response->body());
        }

        $data = $response->json();

        return collect($data['orders'] ?? []);
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
        
        return $orders->map(function($order) {
            return $this->syncOrder($order);
        })->count();
    }

    /**
     * Sincroniza un pedido individual con la base de datos local.
     * 
     * @param array $orderData Datos del pedido desde Shopify
     * @return Order Modelo del pedido sincronizado
     */
    protected function syncOrder(array $orderData): Order
    {
        return Order::updateOrCreate(
            ['id' => $orderData['id']],
            [
                'order_number' => $orderData['order_number'],
                'created_at' => $orderData['created_at'],
                'financial_status' => $orderData['financial_status'],
                'fulfillment_status' => $orderData['fulfillment_status'] ?? null,
                'total_price' => $orderData['total_price'],
                // 'subtotal_price' => $orderData['subtotal_price'],
                // 'total_tax' => $orderData['total_tax'],
                // 'currency' => $orderData['currency'],
                // 'customer_data' => $orderData['customer'] ?? null,
                // 'shipping_address' => $orderData['shipping_address'] ?? null,
                // 'billing_address' => $orderData['billing_address'] ?? null,
                'line_items' => $orderData['line_items']
            ]
        );
    }
}