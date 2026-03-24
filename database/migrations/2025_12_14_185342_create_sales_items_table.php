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
        Schema::create('sales_items', function (Blueprint $table) {
            $table->id();

            $table->foreignId('sale_id')
                ->constrained('sales')
                ->onDelete('cascade');

            $table->foreignId('product_id')
                ->constrained('products')
                ->onDelete('cascade');

            $table->foreignId('product_variant_id')
                ->nullable()
                ->constrained('product_variants')
                ->onDelete('cascade');


            $table->decimal('price', 10, 2);
            $table->integer('quantity');
            $table->decimal('total', 10, 2);



            $table->foreignId('currency_id')
                ->constrained('currencies')
                ->onDelete('cascade');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sales_items');
    }
};
