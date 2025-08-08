<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Symfony\Component\Finder\Iterator\CustomFilterIterator;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->bigInteger('id')->primary();
            $table->string('customer')->nullable();
            $table->bigInteger('order_number');
            $table->string('financial_status');
            $table->decimal('subtotal_price', 10, 2);
            $table->json('line_items')->nullable();
            $table->timestamp('created_at');
            $table->timestamp('updated_at')->useCurrent();
            
            $table->index('order_number');
            $table->index('financial_status');
            $table->index('created_at');
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
