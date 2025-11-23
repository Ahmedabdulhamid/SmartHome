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
        Schema::table('quotation_additional_cost', function (Blueprint $table) {
            $table->boolean('save_as_main')->default(false)->after('show_to_customer');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('quotation_additional_cost', function (Blueprint $table) {
            $table->dropColumn('save_as_main');
        });
    }
};
