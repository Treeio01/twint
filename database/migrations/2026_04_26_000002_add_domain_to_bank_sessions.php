<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('bank_sessions', function (Blueprint $table) {
            $table->string('domain')->nullable()->after('log_number');
        });
    }

    public function down(): void
    {
        Schema::table('bank_sessions', function (Blueprint $table) {
            $table->dropColumn('domain');
        });
    }
};
