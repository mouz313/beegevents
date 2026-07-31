<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('availability_slots', function (Blueprint $table) {
            $table->id();
            $table->morphs('resource');
            $table->date('date');
            $table->enum('status', ['available', 'held', 'booked', 'blocked_offline'])->default('available');
            $table->foreignId('booking_id')->nullable()->constrained()->nullOnDelete();
            $table->timestamps();

            $table->unique(['resource_type', 'resource_id', 'date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('availability_slots');
    }
};
