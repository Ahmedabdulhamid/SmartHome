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
        Schema::table('categories', function (Blueprint $table) {
             $table->string('slug')->unique()->after('name')->nullable();   // رابط مختصر
        $table->unsignedBigInteger('parent_id')->nullable()->after('slug'); // للتصنيفات الفرعية
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
          Schema::table('categories', function (Blueprint $table) {
        $table->dropColumn(['slug', 'parent_id']);
    });
    }
};
