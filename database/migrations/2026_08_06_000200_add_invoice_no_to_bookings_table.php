<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->string('invoice_no', 32)->nullable()->after('id')->index();
        });

        DB::table('bookings')
            ->orderBy('id')
            ->chunkById(200, function ($bookings) {
                foreach ($bookings as $booking) {
                    DB::table('bookings')
                        ->where('id', $booking->id)
                        ->update([
                            'invoice_no' => 'INV-'.date('Y', strtotime($booking->created_at)).'-'.str_pad((string) $booking->id, 6, '0', STR_PAD_LEFT),
                        ]);
                }
            });
    }

    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn('invoice_no');
        });
    }
};
