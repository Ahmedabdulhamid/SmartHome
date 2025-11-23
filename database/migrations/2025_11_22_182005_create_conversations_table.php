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
        Schema::create('conversations', function (Blueprint $table) {
            $table->id();
            $table->string('external_number')->unique();
            $table->string('status')->default('open');
            $table->unsignedBigInteger('assigned_to')->nullable(); // ID الموظف المسؤول عن هذه المحادثة.
            $table->foreign('assigned_to')->references('id')->on('admins')->onDelete('set null');
            $table->timestamp('last_message_at')->nullable();
            $table->integer('unread_count')->default(0);
            $table->index('status');
            $table->index('assigned_to');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('conversations');
    }
};
