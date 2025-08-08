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
        Schema::create('products', function (Blueprint $table) {
            $table->bigInteger('id')->primary(); // ID de Shopify
            $table->string('title');
            $table->string('sku')->nullable();
            $table->decimal('price', 10, 2);
            $table->string('image_url')->nullable();
            $table->json('variants')->nullable(); // Almacenamos los variants como JSON
            $table->json('images')->nullable(); // Almacenamos imágenes como JSON
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
