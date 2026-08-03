<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('booking_items', function (Blueprint $table) {
            $table->unsignedInteger('guests')->nullable()->after('menu_set_id');
            $table->string('catering_mode')->nullable()->after('guests');
        });

        Schema::table('bookings', function (Blueprint $table) {
            $table->timestamp('agreement_accepted_at')->nullable()->after('notes');
        });
    }

    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn('agreement_accepted_at');
        });

        Schema::table('booking_items', function (Blueprint $table) {
            $table->dropColumn(['catering_mode', 'guests']);
        });
    }
};