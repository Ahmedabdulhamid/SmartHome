<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('chatbot_knowledge', function (Blueprint $table) {
            $table->id();
            $table->string('source_type', 30);
            $table->text('title');
            $table->longText('content');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('chatbot_knowledge');
    }
};
