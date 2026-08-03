<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->enum('time_slot', ['noon', 'evening'])->nullable()->after('event_date');
        });

        Schema::table('booking_items', function (Blueprint $table) {
            $table->enum('time_slot', ['noon', 'evening'])->nullable()->after('price');
            $table->json('extras')->nullable()->after('time_slot');
            $table->foreignId('menu_set_id')->nullable()->constrained()->nullOnDelete()->after('extras');
        });
    }

    public function down(): void
    {
        Schema::table('booking_items', function (Blueprint $table) {
            $table->dropConstrainedForeignId('menu_set_id');
            $table->dropColumn(['extras', 'time_slot']);
        });

        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn('time_slot');
        });
    }
};
