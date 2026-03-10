<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('fee_payments', function (Blueprint $table) {
            if (! Schema::hasColumn('fee_payments', 'gross_amount')) {
                $table->decimal('gross_amount', 10, 2)->nullable()->after('amount');
            }
            if (! Schema::hasColumn('fee_payments', 'discount_amount')) {
                $table->decimal('discount_amount', 10, 2)->default(0)->after('gross_amount');
            }
            if (! Schema::hasColumn('fee_payments', 'referral_credit_used')) {
                $table->decimal('referral_credit_used', 10, 2)->default(0)->after('discount_amount');
            }
            if (! Schema::hasColumn('fee_payments', 'promo_code_id')) {
                $table->foreignId('promo_code_id')->nullable()->after('referral_credit_used')->constrained('promo_codes')->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('fee_payments', function (Blueprint $table) {
            if (Schema::hasColumn('fee_payments', 'promo_code_id')) {
                $table->dropConstrainedForeignId('promo_code_id');
            }
            $drops = [];
            foreach (['gross_amount', 'discount_amount', 'referral_credit_used'] as $col) {
                if (Schema::hasColumn('fee_payments', $col)) {
                    $drops[] = $col;
                }
            }
            if (!empty($drops)) {
                $table->dropColumn($drops);
            }
        });
    }
};
