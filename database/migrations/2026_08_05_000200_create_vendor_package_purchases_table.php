<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vendor_package_purchases', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vendor_profile_id')->constrained()->cascadeOnDelete();
            $table->foreignId('package_id')->constrained()->cascadeOnDelete();
            $table->decimal('amount', 10, 2)->default(0);
            $table->enum('method', ['card', 'bank_transfer', 'manual'])->default('card');
            $table->enum('status', ['pending', 'active', 'expired', 'refunded'])->default('pending');
            $table->unsignedInteger('duration_days')->nullable();
            $table->unsignedInteger('max_halls')->nullable();
            $table->unsignedInteger('max_listings')->nullable();
            $table->enum('boost_tier', ['featured', 'premium'])->nullable();
            $table->timestamp('starts_at')->nullable();
            $table->timestamp('ends_at')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->string('transaction_id')->nullable();
            $table->string('proof_path')->nullable();
            $table->string('notes')->nullable();
            $table->timestamps();

            $table->index(['vendor_profile_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vendor_package_purchases');
    }
};
