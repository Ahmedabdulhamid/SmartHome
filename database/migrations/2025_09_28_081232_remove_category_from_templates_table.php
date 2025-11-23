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
        Schema::table('templates', function (Blueprint $table) {
            $table->dropColumn('category');
            $table->json('header_html')->nullable();
            $table->json('body_html')->nullable();
            $table->json('footer_html')->nullable();
            $table->string('default_font')->default('dejavusans');
            $table->string('logo')->nullable();
            $table->string('color_scheme')->default('light');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('templates', function (Blueprint $table) {
            //
        });
    }
};
