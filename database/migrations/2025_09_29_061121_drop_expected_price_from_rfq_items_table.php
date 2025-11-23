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
        Schema::table('rfq_items', function (Blueprint $table) {
            $table->dropColumn('expected_price');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('rfq_items', function (Blueprint $table) {
           $table->decimal('expected_price', 10, 2)->nullable();
        });
    }
};
