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
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->unsignedBigInteger(('payment_method_id'));
            $table->decimal('total_amount', 10, 2);
            $table->foreign('payment_method_id')->references('id')->on('paym_methods')->onDelete('cascade');
            $table->string('f_name');
            $table->string('l_name');
            $table->string('email');
            $table->string('phone');
            $table->string('address');
            $table->unsignedBigInteger('city_id');
            $table->foreign('city_id')->references('id')->on('cities')->onDelete('cascade');
            $table->unsignedBigInteger('governorate_id');
            $table->foreign('governorate_id')->references('id')->on('governorates')->onDelete('cascade');
            $table->string('zip_code');
            $table->enum('status', [
                'pending',
                'confirmed',
                'shipped',
                'delivered',
                'cancelled'
            ])->default('pending');
            $table->decimal('shipping_price', 10, 2)->nullable();
            $table->unsignedBigInteger('currency_id');
            $table->foreign('currency_id')->references('id')->on('currencies')->onDelete('cascade');
            $table->softDeletes();
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
