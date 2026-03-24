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
        Schema::create('sales', function (Blueprint $table) {
    $table->id();

    // الربط مع الطلب
    $table->foreignId('order_id')
        ->constrained('orders')
        ->onDelete('cascade');

    $table->foreignId('user_id')
        ->constrained()
        ->nullable()
        ->onDelete('cascade');

    // الإجماليات
    $table->decimal('subtotal', 10, 2);
    $table->decimal('shipping_price', 10, 2)->default(0);
    $table->decimal('discount', 10, 2)->default(0);
    $table->decimal('tax', 10, 2)->default(0);
    $table->decimal('total_amount', 10, 2);

    // حالة البيع
    $table->enum('status', [
        'pending',
        'paid',
        'partially_paid',
        'refunded',
        'cancelled'
    ])->default('pending');

    // العملة
    $table->foreignId('currency_id')
        ->constrained('currencies')
        ->onDelete('cascade');

    // تاريخ البيع الفعلي
    $table->timestamp('sold_at')->nullable();

    $table->timestamps();
});

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sales');
    }
};
