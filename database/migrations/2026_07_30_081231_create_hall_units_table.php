<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hall_units', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hall_id')->constrained()->cascadeOnDelete();
            $table->foreignId('floor_id')->nullable()->constrained()->nullOnDelete();
            $table->string('unit_name');
            $table->integer('min_capacity');
            $table->integer('max_capacity');
            $table->text('menu_summary')->nullable();
            $table->enum('decor_type', ['fixed', 'outsourced', 'customizable'])->default('fixed');
            $table->decimal('base_price', 10, 2);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hall_units');
    }
};
