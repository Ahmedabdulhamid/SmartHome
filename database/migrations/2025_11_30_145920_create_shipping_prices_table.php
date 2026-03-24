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
        Schema::create('shipping_prices', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('governorate_id');
            $table->unsignedBigInteger('city_id')->nullable();

            $table->enum('shipping_type', ['standard', 'express']);
            $table->integer('estimated_days')->nullable();

            // السعر الأساسي
            $table->decimal('price', 8, 2);

            // الوزن
            $table->decimal('min_weight', 8, 2)->default(0);
            $table->decimal('max_weight', 8, 2)->nullable();

            // مصاريف الإرجاع
            $table->decimal('return_fee', 8, 2)->default(0);

            // عملة
            $table->unsignedBigInteger('currency_id');

            $table->foreign('governorate_id')->references('id')->on('governorates')->onDelete('cascade');
            $table->foreign('city_id')->references('id')->on('cities')->onDelete('cascade');
            $table->foreign('currency_id')->references('id')->on('currencies')->onDelete('cascade');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shipping_prices');
    }
};
