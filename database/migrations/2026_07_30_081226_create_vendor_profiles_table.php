<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vendor_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('business_name');
            $table->enum('vendor_type', ['hall', 'farmhouse', 'decor', 'catering', 'photography', 'dj', 'car', 'other']);
            $table->string('city')->default('Lahore');
            $table->enum('status', ['pending', 'verified', 'suspended'])->default('pending');
            $table->text('cancellation_policy')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vendor_profiles');
    }
};
