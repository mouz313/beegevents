<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vendor_combo_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('combo_id')->constrained('vendor_combos')->cascadeOnDelete();
            $table->morphs('itemable');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vendor_combo_items');
    }
};
