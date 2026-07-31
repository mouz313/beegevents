<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained('users')->cascadeOnDelete();
            $table->enum('booking_type', ['single', 'multi', 'package', 'custom'])->default('single');
            $table->date('event_date');
            $table->enum('event_type', ['wedding', 'engagement', 'corporate', 'birthday', 'home', 'other'])->default('other');
            $table->enum('status', ['requested', 'discussing', 'verified', 'confirmed', 'completed', 'cancelled'])->default('requested');
            $table->decimal('budget_input', 10, 2)->nullable();
            $table->decimal('total_price', 10, 2)->default(0);
            $table->decimal('commission_amount', 10, 2)->default(0);
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
