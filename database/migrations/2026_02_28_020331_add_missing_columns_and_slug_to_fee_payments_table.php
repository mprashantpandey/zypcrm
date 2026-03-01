<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('fee_payments', function (Blueprint $table) {
            if (!Schema::hasColumn('fee_payments', 'payment_method')) {
                $table->string('payment_method')->nullable()->after('status');
            }
            if (!Schema::hasColumn('fee_payments', 'transaction_id')) {
                $table->string('transaction_id')->nullable()->after('payment_method');
            }
            if (!Schema::hasColumn('fee_payments', 'slug')) {
                $table->string('slug')->unique()->nullable()->after('id');
            }
        });

        // Backfill slugs
        $payments = \Illuminate\Support\Facades\DB::table('fee_payments')->whereNull('slug')->get();
        foreach ($payments as $payment) {
            \Illuminate\Support\Facades\DB::table('fee_payments')->where('id', $payment->id)->update(['slug' => \Illuminate\Support\Str::random(12)]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('fee_payments', function (Blueprint $table) {
            $table->dropColumn(['payment_method', 'transaction_id', 'slug']);
        });
    }
};