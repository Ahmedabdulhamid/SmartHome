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
        Schema::dropIfExists('category_translations');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
       Schema::create('category_translations', function (Blueprint $table) {
        $table->id();
        $table->unsignedBigInteger('category_id');
        $table->string('locale')->index();
        $table->string('name');
        $table->timestamps();

        $table->unique(['category_id', 'locale']);
    });
    }
};
