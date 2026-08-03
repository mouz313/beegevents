<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('availability_slots', 'slot_type')) {
            Schema::table('availability_slots', function (Blueprint $table) {
                $table->string('slot_type', 20)->nullable()->after('time_slot');
            });
        }
    }

    public function down(): void
    {
        Schema::table('availability_slots', function (Blueprint $table) {
            $table->dropColumn('slot_type');
        });
    }
};
