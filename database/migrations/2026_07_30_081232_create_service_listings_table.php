<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('service_listings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vendor_profile_id')->constrained()->cascadeOnDelete();
            $table->foreignId('service_category_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->text('description')->nullable();
            $table->decimal('price', 10, 2);
            $table->enum('price_unit', ['fixed', 'per_person', 'per_hour'])->default('fixed');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('service_listings');
    }
};
