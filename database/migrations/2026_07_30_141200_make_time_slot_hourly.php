<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        try {
            Schema::table('availability_slots', function (Blueprint $table) {
                $table->dropUnique('avail_slots_type_id_date_time_unique');
            });
        } catch (\Exception $e) {
            // may not exist
        }

        Schema::table('availability_slots', function (Blueprint $table) {
            $table->integer('time_slot')->nullable()->change();
        });

        try {
            Schema::table('availability_slots', function (Blueprint $table) {
                $table->unique(['resource_type', 'resource_id', 'date', 'time_slot'], 'avail_slots_type_id_date_time_unique');
            });
        } catch (\Exception $e) {
            // may already exist
        }
    }

    public function down(): void
    {
        try {
            Schema::table('availability_slots', function (Blueprint $table) {
                $table->dropUnique('avail_slots_type_id_date_time_unique');
            });
        } catch (\Exception $e) {
            // may not exist
        }

        Schema::table('availability_slots', function (Blueprint $table) {
            $table->string('time_slot', 20)->nullable()->change();
        });

        try {
            Schema::table('availability_slots', function (Blueprint $table) {
                $table->unique(['resource_type', 'resource_id', 'date', 'time_slot'], 'avail_slots_type_id_date_time_unique');
            });
        } catch (\Exception $e) {
            // may already exist
        }
    }
};
