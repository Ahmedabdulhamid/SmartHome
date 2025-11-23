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
        Schema::create('product_variants', function (Blueprint $table) {
            $table->id();

            // الربط بالمنتج
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();

            $table->json('name'); // مثال: White – WiFi
            $table->string('protocol')->nullable(); // WiFi, Zigbee, Bluetooth
            $table->string('sku')->unique()->nullable();
            $table->string('color')->nullable();   // اللون
            $table->string('size')->nullable();    // الحجم (Small, Medium, Large)
            $table->decimal('price', 10, 2);
            $table->boolean('manage_quantity')->default(true);
            $table->integer('quantity')->default(0);

            $table->timestamps();
        });
    }


    public function down(): void
    {
        Schema::dropIfExists('product_variants');
    }
};
