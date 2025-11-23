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
        Schema::create('data_sheets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')
                  ->constrained() // يفترض وجود جدول 'products'
                  ->onDelete('cascade');
                  $table->string('file_path');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('data_sheets');
    }
};
