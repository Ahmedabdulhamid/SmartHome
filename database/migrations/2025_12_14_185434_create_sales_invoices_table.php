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
        Schema::create('sales_invoices', function (Blueprint $table) {
            $table->id();

            $table->foreignId('sale_id')
                ->constrained('sales')
                ->onDelete('cascade');

            // رقم الفاتورة
            $table->string('invoice_number')->unique();

            // بيانات الفاتورة
            $table->decimal('subtotal', 10, 2);
            $table->decimal('tax', 10, 2)->default(0);
            $table->decimal('shipping_price', 10, 2)->default(0);
            $table->decimal('discount', 10, 2)->default(0);
            $table->decimal('total_amount', 10, 2);

            $table->foreignId('currency_id')
                ->constrained('currencies')
                ->onDelete('cascade');

            // حالة الفاتورة
            $table->enum('status', [
                'unpaid',
                'paid',
                'cancelled',
                'refunded'
            ])->default('unpaid');

            $table->timestamp('issued_at')->nullable();
            $table->timestamp('paid_at')->nullable();

            // رابط PDF
            $table->string('pdf_path')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sales_invoices');
    }
};
