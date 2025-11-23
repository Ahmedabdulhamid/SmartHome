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
        Schema::create('chat_messages', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('conversation_id');
            $table->foreign('conversation_id')->references('id')->on('conversations')->onDelete('cascade');
            $table->unsignedBigInteger('admin_id')->nullable();
            $table->foreign('admin_id')->references('id')->on('admins')->onDelete('set null');
            $table->string('direction'); // 'inbound' (من العميل) أو 'outbound' (من الموظف)
            $table->string('external_number'); // رقم هاتف العميل (whatsapp:+XXXX)
            $table->enum('type', ['text', 'image', 'video', 'voice'])->default('text'); // text, image, video
            $table->longText('body')->nullable();
            $table->string('attachment_url')->nullable();
            $table->string('status')->default('pending'); // pending, sent, delivered, read, failed
            $table->boolean('seen_by_user')->default(false); // هل رآها الموظف؟
            $table->index('conversation_id');
            $table->index('external_number');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chat_messages');
    }
};
