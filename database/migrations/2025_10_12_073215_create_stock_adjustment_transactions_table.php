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
        Schema::create('stock_adjustment_transactions', function (Blueprint $table) {
            $table->id();

            // حقول العلاقة Polymorphic
            $table->unsignedBigInteger('adjustable_id');
            $table->string('adjustable_type');

            $table->integer('quantity_changed');
            $table->enum('adjustment_type', ['IN', 'OUT'])->default('IN');
            $table->text('reason');

            // ⚠️ إضافة حقل المستخدم (ضروري للتدقيق)
            $table->foreignId('admin_id')->constrained('admins');

            // ✅ حل مشكلة طول اسم الفهرس: تحديد اسم فهرس قصير يدوياً
            $table->index(['adjustable_id', 'adjustable_type'], 'stock_adj_adj_idx');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_adjustment_transactions');
    }
};
