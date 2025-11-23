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
        Schema::table('quotations', function (Blueprint $table) {
            $table->decimal('manual_base_price', 15, 4)->nullable()->before('total')->comment('Used when RFQ has expected price without selected products');
            $table->decimal('manual_margin_percentage', 5, 2)->nullable()->before('total')->comment('Used when RFQ has expected price without selected products');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('quotations', function (Blueprint $table) {
            //
        });
    }
};
