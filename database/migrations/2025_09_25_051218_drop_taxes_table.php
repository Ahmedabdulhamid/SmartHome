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
        Schema::dropIfExists('taxes');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::create('taxes', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            // ضيف الأعمدة اللي كانت موجودة عندك قبل لو محتاج ترجع الجدول
        });
    }
};
