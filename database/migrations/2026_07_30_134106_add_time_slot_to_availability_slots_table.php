<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasColumn('availability_slots', 'time_slot')) {
            Schema::table('availability_slots', function (Blueprint $table) {
                $table->string('time_slot', 20)->nullable()->after('date');
                $table->text('notes')->nullable()->after('status');
                $table->timestamp('held_until')->nullable()->after('notes');
            });
        }

        try {
            Schema::table('availability_slots', function (Blueprint $table) {
                $table->dropUnique('availability_slots_resource_type_resource_id_date_unique');
            });
        } catch (\Exception $e) {
            // may already be dropped
        }

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
                $table->dropColumn(['time_slot', 'notes', 'held_until']);
            });
        } catch (\Exception $e) {
            // already dropped or doesn't exist
        }
    }
};
