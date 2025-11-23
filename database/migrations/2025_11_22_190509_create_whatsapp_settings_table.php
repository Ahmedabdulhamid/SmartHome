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
        Schema::create('whatsapp_settings', function (Blueprint $table) {
        $table->id();
        $table->string('meta_app_id');
        $table->string('phone_number_id');
        $table->string('whatsapp_business_account_id');

        // تم التعديل إلى 'text' هنا:
        $table->text('meta_access_token');

        $table->string('meta_verify_token');
        $table->timestamps();
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('whatsapp_settings');
    }
};
