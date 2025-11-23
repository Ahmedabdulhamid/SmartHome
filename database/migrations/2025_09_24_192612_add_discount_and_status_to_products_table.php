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
       Schema::table('products', function (Blueprint $table) {
            $table->boolean('has_discount')->default(false)->after('base_price'); // يوجد خصم أو لا
            $table->timestamp('start_at')->nullable()->after('has_discount'); // بداية الخصم
            $table->timestamp('ends_at')->nullable()->after('start_at');     // نهاية الخصم
            $table->enum('status',['active','inactive'])->default('active')->after('ends_at');      // حالة التفعيل
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
             $table->dropColumn(['has_discount', 'start_at', 'ends_at', 'status']);
        });
    }
};
