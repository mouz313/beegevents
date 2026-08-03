<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE vendor_profiles MODIFY status ENUM('pending', 'verified', 'suspended', 'blocked') NOT NULL DEFAULT 'pending'");

        Schema::table('vendor_profiles', function (Blueprint $table) {
            $table->timestamp('trial_ends_at')->nullable()->after('status');
        });
    }

    public function down(): void
    {
        Schema::table('vendor_profiles', function (Blueprint $table) {
            $table->dropColumn('trial_ends_at');
        });

        DB::statement("ALTER TABLE vendor_profiles MODIFY status ENUM('pending', 'verified', 'suspended') NOT NULL DEFAULT 'pending'");
    }
};
