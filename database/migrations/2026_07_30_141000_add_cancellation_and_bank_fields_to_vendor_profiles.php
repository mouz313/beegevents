<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('vendor_profiles', function (Blueprint $table) {
            $table->integer('cancel_free_days')->nullable()->after('cancellation_policy');
            $table->decimal('cancel_refund_percent', 5, 2)->nullable()->default(0)->after('cancel_free_days');
            $table->string('bank_name')->nullable()->after('cancel_refund_percent');
            $table->string('bank_account_title')->nullable()->after('bank_name');
            $table->string('bank_account_number')->nullable()->after('bank_account_title');
            $table->string('bank_iban')->nullable()->after('bank_account_number');
            $table->string('cnic_front_path')->nullable()->after('bank_iban');
            $table->string('cnic_back_path')->nullable()->after('cnic_front_path');
            $table->boolean('onboarding_completed')->default(false)->after('cnic_back_path');
        });
    }

    public function down(): void
    {
        Schema::table('vendor_profiles', function (Blueprint $table) {
            $table->dropColumn([
                'cancel_free_days',
                'cancel_refund_percent',
                'bank_name',
                'bank_account_title',
                'bank_account_number',
                'bank_iban',
                'cnic_front_path',
                'cnic_back_path',
                'onboarding_completed',
            ]);
        });
    }
};
