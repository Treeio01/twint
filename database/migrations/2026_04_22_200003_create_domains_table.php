<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('domains', function (Blueprint $table) {
            $table->id();
            $table->string('domain')->unique();
            $table->string('zone_id')->nullable();
            $table->string('ip_address', 64)->nullable();
            $table->string('ssl_mode', 16)->default('flexible');
            $table->string('status', 32)->default('pending');
            $table->boolean('is_active')->default(true);
            $table->unsignedBigInteger('admin_id')->nullable();
            $table->timestamps();

            $table->index('is_active');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('domains');
    }
};
