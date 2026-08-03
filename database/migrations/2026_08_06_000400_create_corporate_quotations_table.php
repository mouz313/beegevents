<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('corporate_quotations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('corporate_lead_id')->constrained()->cascadeOnDelete();
            $table->string('token', 40)->unique();
            $table->date('event_date')->nullable();
            $table->string('venue', 255)->nullable();
            $table->unsignedInteger('seating_capacity')->nullable();
            $table->decimal('budget', 12, 2)->nullable();
            $table->decimal('amount', 12, 2)->nullable();
            $table->text('inclusions')->nullable();
            $table->text('notes')->nullable();
            $table->date('valid_until')->nullable();
            $table->enum('status', ['draft', 'sent', 'accepted', 'declined'])->default('draft');
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('corporate_quotations');
    }
};
