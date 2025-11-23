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
        Schema::create('feature_service', function (Blueprint $table) {
            $table->id();
            $table->foreignId('feature_id')
                  ->constrained('features')
                  ->onDelete('cascade');

            // ربط بجدول services.
            $table->foreignId('service_id')
                  ->constrained('services')
                  ->onDelete('cascade');


                $table->decimal('additional_cost', 8, 2)->nullable();


            $table->foreignId('currency_id')
                  ->constrained('currencies')
                  ->onDelete('restrict');


            $table->unique(['feature_id', 'service_id', 'currency_id']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('feature_service');
    }
};
