<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tenant_subscription_invoices', function (Blueprint $table) {
            $table->timestamp('receipt_emailed_at')->nullable()->after('paid_at');
            $table->unsignedInteger('receipt_email_attempts')->default(0)->after('receipt_emailed_at');
        });
    }

    public function down(): void
    {
        Schema::table('tenant_subscription_invoices', function (Blueprint $table) {
            $table->dropColumn(['receipt_emailed_at', 'receipt_email_attempts']);
        });
    }
};

