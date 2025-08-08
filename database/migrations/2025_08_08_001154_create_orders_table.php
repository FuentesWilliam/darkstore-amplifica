<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->bigInteger('id')->primary(); // ID de Shopify
            $table->bigInteger('order_number')->unique();
            $table->string('financial_status'); // paid, pending, refunded, etc.
            $table->string('fulfillment_status')->nullable(); // fulfilled, partial, null
            $table->decimal('total_price', 10, 2);
            $table->json('customer_data')->nullable(); // Datos del cliente en JSON
            $table->json('line_items'); // Items del pedido en JSON
            $table->json('shipping_address')->nullable();
            $table->json('billing_address')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
