<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('vendor_combo_items', function (Blueprint $table) {
            $table->unsignedInteger('slot_index')->nullable()->after('combo_id');
        });
    }

    public function down(): void
    {
        Schema::table('vendor_combo_items', function (Blueprint $table) {
            $table->dropColumn('slot_index');
        });
    }
};
