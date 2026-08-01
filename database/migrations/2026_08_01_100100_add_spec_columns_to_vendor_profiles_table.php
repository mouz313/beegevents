<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('vendor_profiles', function (Blueprint $table) {
            $table->string('contact_person_name')->nullable()->after('address');
            $table->string('contact_person_phone', 20)->nullable()->after('contact_person_name');
            $table->string('legal_doc_path')->nullable()->after('contact_person_phone');
            $table->integer('min_capacity')->nullable()->after('legal_doc_path');
            $table->integer('max_capacity')->nullable()->after('min_capacity');
            $table->decimal('starting_price', 12, 2)->nullable()->after('max_capacity');
            $table->unsignedSmallInteger('years_experience')->nullable()->after('starting_price');
            $table->json('type_specs')->nullable()->after('years_experience');
        });
    }

    public function down(): void
    {
        Schema::table('vendor_profiles', function (Blueprint $table) {
            $table->dropColumn([
                'contact_person_name',
                'contact_person_phone',
                'legal_doc_path',
                'min_capacity',
                'max_capacity',
                'starting_price',
                'years_experience',
                'type_specs',
            ]);
        });
    }
};
