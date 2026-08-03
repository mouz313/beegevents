<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('vendor_profiles', function (Blueprint $table) {
            $table->enum('feature_tier', ['featured', 'premium'])->nullable()->after('status');
            $table->timestamp('featured_until')->nullable()->after('feature_tier');
        });
    }

    public function down(): void
    {
        Schema::table('vendor_profiles', function (Blueprint $table) {
            $table->dropColumn(['feature_tier', 'featured_until']);
        });
    }
};
