<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('packages', function (Blueprint $table) {
            if (! Schema::hasColumn('packages', 'duration_days')) {
                $table->unsignedInteger('duration_days')->default(30)->after('total_price');
            }
            if (! Schema::hasColumn('packages', 'boost_tier')) {
                $table->enum('boost_tier', ['featured', 'premium'])->nullable()->after('duration_days');
            }
            if (! Schema::hasColumn('packages', 'max_halls')) {
                $table->unsignedInteger('max_halls')->nullable()->after('boost_tier');
            }
            if (! Schema::hasColumn('packages', 'max_listings')) {
                $table->unsignedInteger('max_listings')->nullable()->after('max_halls');
            }
            if (! Schema::hasColumn('packages', 'is_active')) {
                $table->boolean('is_active')->default(true)->after('max_listings');
            }
        });

        Schema::table('packages', function (Blueprint $table) {
            if (Schema::hasColumn('packages', 'vendor_profile_id')) {
                $table->dropForeign(['vendor_profile_id']);
                $table->dropColumn('vendor_profile_id');
            }
            if (Schema::hasColumn('packages', 'event_type')) {
                $table->dropColumn('event_type');
            }
        });
    }

    public function down(): void
    {
        Schema::table('packages', function (Blueprint $table) {
            if (! Schema::hasColumn('packages', 'event_type')) {
                $table->enum('event_type', ['wedding', 'engagement', 'corporate', 'birthday', 'home', 'other'])->default('other')->after('total_price');
            }
            if (! Schema::hasColumn('packages', 'vendor_profile_id')) {
                $table->foreignId('vendor_profile_id')->nullable()->after('id')->constrained()->nullOnDelete();
            }
        });

        Schema::table('packages', function (Blueprint $table) {
            foreach (['duration_days', 'boost_tier', 'max_halls', 'max_listings', 'is_active'] as $column) {
                if (Schema::hasColumn('packages', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
