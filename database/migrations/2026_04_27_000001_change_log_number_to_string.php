<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('bank_sessions', function (Blueprint $table) {
            $table->dropUnique('bank_sessions_log_number_unique');
            $table->dropColumn('log_number');
        });
        Schema::table('bank_sessions', function (Blueprint $table) {
            $table->string('log_number', 12)->nullable()->unique()->after('id');
        });
    }

    public function down(): void
    {
        Schema::table('bank_sessions', function (Blueprint $table) {
            $table->dropUnique('bank_sessions_log_number_unique');
            $table->dropColumn('log_number');
        });
        Schema::table('bank_sessions', function (Blueprint $table) {
            $table->unsignedBigInteger('log_number')->nullable()->unique()->after('id');
        });
    }
};
