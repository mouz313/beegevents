<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->decimal('negotiated_price', 12, 2)->nullable()->after('total_price');
            $table->decimal('price_offer', 12, 2)->nullable()->after('negotiated_price');
            $table->string('price_offer_status')->nullable()->after('price_offer');
            $table->string('price_offer_note')->nullable()->after('price_offer_status');
            $table->timestamp('price_offer_sent_at')->nullable()->after('price_offer_note');
            $table->string('price_negotiation_note')->nullable()->after('price_offer_sent_at');
        });
    }

    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn([
                'negotiated_price',
                'price_offer',
                'price_offer_status',
                'price_offer_note',
                'price_offer_sent_at',
                'price_negotiation_note',
            ]);
        });
    }
};
