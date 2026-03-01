<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('subscription_plans', function (Blueprint $table) {
            $table->string('stripe_price_id')->nullable()->after('max_students');
            $table->string('razorpay_plan_id')->nullable()->after('stripe_price_id');
            $table->json('features')->nullable()->after('razorpay_plan_id');
        });
    }

    public function down(): void
    {
        Schema::table('subscription_plans', function (Blueprint $table) {
            $table->dropColumn(['stripe_price_id', 'razorpay_plan_id', 'features']);
        });
    }
};