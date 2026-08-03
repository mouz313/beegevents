<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('vendor_feature_purchases');
    }

    public function down(): void
    {
        Schema::create('vendor_feature_purchases', function ($table) {
            $table->id();
            $table->foreignId('vendor_profile_id')->constrained()->cascadeOnDelete();
            $table->enum('tier', ['featured', 'premium'])->default('featured');
            $table->unsignedInteger('duration_days')->default(30);
            $table->decimal('amount', 10, 2)->default(0);
            $table->enum('method', ['card', 'bank_transfer'])->default('card');
            $table->enum('status', ['pending', 'active', 'expired', 'refunded'])->default('pending');
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
};
