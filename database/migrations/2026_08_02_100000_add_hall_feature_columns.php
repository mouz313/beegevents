<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('halls', function (Blueprint $table) {
            $table->string('venue_type')->nullable()->after('name');
        });

        Schema::table('hall_units', function (Blueprint $table) {
            $table->enum('catering_mode', ['internal', 'external', 'both', 'none'])->nullable()->after('decor_type');
            $table->enum('food_service_style', ['static_place', 'on_table', 'both'])->nullable()->after('catering_mode');
            $table->unsignedSmallInteger('staff_male')->default(0)->after('food_service_style');
            $table->unsignedSmallInteger('staff_female')->default(0)->after('staff_male');
            $table->json('amenities')->nullable()->after('staff_female');
        });
    }

    public function down(): void
    {
        Schema::table('hall_units', function (Blueprint $table) {
            $table->dropColumn(['catering_mode', 'food_service_style', 'staff_male', 'staff_female', 'amenities']);
        });

        Schema::table('halls', function (Blueprint $table) {
            $table->dropColumn('venue_type');
        });
    }
};
