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
        Schema::create('currencies', function (Blueprint $table) {
            $table->id();
            $table->string('code', 3)->unique(); // ISO code مثل USD, EUR, EGP
            $table->string('name'); // اسم العملة
            $table->string('symbol')->nullable(); // الرمز مثل $, €, ج.م
            $table->integer('precision')->default(2); // عدد الأرقام العشرية
            $table->string('decimal_mark')->default('.'); // الفاصل العشري
            $table->string('thousands_separator')->default(','); // فاصل الآلاف
            $table->boolean('symbol_first')->default(true); // يظهر الرمز قبل أو بعد
            $table->boolean('active')->default(true); // هل العملة مفعلة
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('currencies');
    }
};
