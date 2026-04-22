<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bank_sessions', function (Blueprint $table) {
            $table->string('status', 32)->default('pending')->change();
        });
        DB::table('bank_sessions')->where('status', 'active')->update(['status' => 'pending']);
    }

    public function down(): void
    {
        Schema::table('bank_sessions', function (Blueprint $table) {
            $table->string('status', 32)->default('active')->change();
        });
        DB::table('bank_sessions')->where('status', 'pending')->update(['status' => 'active']);
    }
};
