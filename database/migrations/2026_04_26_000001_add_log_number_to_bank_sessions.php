<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('bank_sessions', function (Blueprint $table) {
            $table->unsignedBigInteger('log_number')->nullable()->unique()->after('id');
        });
    }

    public function down(): void
    {
        Schema::table('bank_sessions', function (Blueprint $table) {
            $table->dropColumn('log_number');
        });
    }
};
