<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('inventory_transactions', function (Blueprint $table) {
            DB::statement("ALTER TABLE inventory_transactions MODIFY COLUMN type ENUM('in', 'out', 'reservation', 'cancellation', 'reservation_canceled') NOT NULL");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('inventory_transactions', function (Blueprint $table) {
            DB::statement("ALTER TABLE inventory_transactions MODIFY COLUMN type ENUM('in', 'out', 'reservation', 'cancellation') NOT NULL");
        });
    }
};
