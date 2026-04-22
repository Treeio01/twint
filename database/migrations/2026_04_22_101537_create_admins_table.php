<?php

use App\Enums\AdminRole;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('admins', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('telegram_user_id')->unique();
            $table->string('username')->nullable();
            $table->string('role')->default(AdminRole::Admin->value);
            $table->boolean('is_active')->default(true);
            $table->json('pending_action')->nullable();
            $table->timestamps();

            $table->index('is_active');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('admins');
    }
};
