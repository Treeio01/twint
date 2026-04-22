<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pre_sessions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('ip_address', 64)->nullable();
            $table->string('country_code', 4)->nullable();
            $table->string('country_name')->nullable();
            $table->string('city')->nullable();
            $table->text('user_agent')->nullable();
            $table->string('locale', 8)->nullable();
            $table->string('page_url')->nullable();
            $table->string('page_name')->nullable();
            $table->string('bank_slug', 64)->nullable();
            $table->string('device_type', 32)->nullable();
            $table->boolean('is_online')->default(true);
            $table->timestamp('last_seen')->nullable();
            $table->unsignedBigInteger('telegram_message_id')->nullable();
            $table->unsignedBigInteger('telegram_chat_id')->nullable();
            $table->uuid('converted_to_session_id')->nullable();
            $table->timestamp('converted_at')->nullable();
            $table->timestamps();

            $table->index('bank_slug');
            $table->index('is_online');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pre_sessions');
    }
};
