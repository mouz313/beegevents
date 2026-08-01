<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hall_specs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vendor_profile_id')->constrained()->cascadeOnDelete();
            $table->unsignedSmallInteger('floors_count')->nullable();
            $table->integer('min_person_booking')->nullable();
            $table->integer('max_person_booking')->nullable();
            $table->decimal('sitting_per_person_cost', 12, 2)->nullable();
            $table->decimal('hall_cost', 12, 2)->nullable();
            $table->decimal('per_person_without_food', 12, 2)->nullable();
            $table->decimal('hall_cost_without_food', 12, 2)->nullable();
            $table->boolean('allows_catering')->default(false);
            $table->boolean('allows_photography')->default(false);
            $table->boolean('allows_dj_light')->default(false);
            $table->boolean('allows_decor')->default(false);
            $table->json('amenities')->nullable();
            $table->timestamps();
        });

        Schema::create('farmhouse_specs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vendor_profile_id')->constrained()->cascadeOnDelete();
            $table->decimal('land_area_kanal', 8, 2)->nullable();
            $table->decimal('sitting_per_person_cost', 12, 2)->nullable();
            $table->decimal('venue_cost', 12, 2)->nullable();
            $table->decimal('per_person_without_food', 12, 2)->nullable();
            $table->decimal('venue_cost_without_food', 12, 2)->nullable();
            $table->boolean('allows_catering')->default(false);
            $table->boolean('allows_photography')->default(false);
            $table->boolean('allows_dj_light')->default(false);
            $table->boolean('allows_decor')->default(false);
            $table->json('amenities')->nullable();
            $table->timestamps();
        });

        Schema::create('decor_specs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vendor_profile_id')->constrained()->cascadeOnDelete();
            $table->unsignedSmallInteger('years_experience')->nullable();
            $table->json('styles')->nullable();
            $table->boolean('includes_lighting')->default(false);
            $table->string('setup_time')->nullable();
            $table->string('service_area')->nullable();
            $table->decimal('base_price', 12, 2)->nullable();
            $table->timestamps();
        });

        Schema::create('catering_specs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vendor_profile_id')->constrained()->cascadeOnDelete();
            $table->unsignedSmallInteger('years_experience')->nullable();
            $table->json('cuisine_types')->nullable();
            $table->decimal('price_per_person_min', 12, 2)->nullable();
            $table->decimal('price_per_person_max', 12, 2)->nullable();
            $table->integer('max_capacity_per_event')->nullable();
            $table->boolean('halal_certified')->default(false);
            $table->string('service_area')->nullable();
            $table->timestamps();
        });

        Schema::create('photography_specs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vendor_profile_id')->constrained()->cascadeOnDelete();
            $table->unsignedSmallInteger('years_experience')->nullable();
            $table->json('coverage_types')->nullable();
            $table->unsignedSmallInteger('team_size')->nullable();
            $table->boolean('includes_videography')->default(false);
            $table->boolean('includes_drone')->default(false);
            $table->decimal('base_price', 12, 2)->nullable();
            $table->timestamps();
        });

        Schema::create('dj_specs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vendor_profile_id')->constrained()->cascadeOnDelete();
            $table->string('setup_type')->nullable();
            $table->json('equipment')->nullable();
            $table->unsignedSmallInteger('years_experience')->nullable();
            $table->decimal('base_price', 12, 2)->nullable();
            $table->timestamps();
        });

        Schema::create('car_specs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vendor_profile_id')->constrained()->cascadeOnDelete();
            $table->unsignedSmallInteger('fleet_size')->nullable();
            $table->json('vehicle_types')->nullable();
            $table->boolean('chauffeur_included')->default(false);
            $table->decimal('per_event_price', 12, 2)->nullable();
            $table->decimal('per_hour_price', 12, 2)->nullable();
            $table->string('service_area')->nullable();
            $table->timestamps();
        });

        Schema::create('other_specs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vendor_profile_id')->constrained()->cascadeOnDelete();
            $table->text('details')->nullable();
            $table->decimal('base_price', 12, 2)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hall_specs');
        Schema::dropIfExists('farmhouse_specs');
        Schema::dropIfExists('decor_specs');
        Schema::dropIfExists('catering_specs');
        Schema::dropIfExists('photography_specs');
        Schema::dropIfExists('dj_specs');
        Schema::dropIfExists('car_specs');
        Schema::dropIfExists('other_specs');
    }
};
