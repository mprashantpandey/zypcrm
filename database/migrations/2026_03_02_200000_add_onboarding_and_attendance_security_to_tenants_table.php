<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('tenants', function (Blueprint $table) {
            if (! Schema::hasColumn('tenants', 'attendance_security_settings')) {
                $table->json('attendance_security_settings')->nullable()->after('operating_hours');
            }
            if (! Schema::hasColumn('tenants', 'attendance_registered_device_hash')) {
                $table->string('attendance_registered_device_hash', 64)->nullable()->after('attendance_security_settings');
            }
            if (! Schema::hasColumn('tenants', 'onboarding_completed_at')) {
                $table->timestamp('onboarding_completed_at')->nullable()->after('attendance_registered_device_hash');
            }
            if (! Schema::hasColumn('tenants', 'onboarding_dismissed_at')) {
                $table->timestamp('onboarding_dismissed_at')->nullable()->after('onboarding_completed_at');
            }
        });
    }

    public function down(): void
    {
        Schema::table('tenants', function (Blueprint $table) {
            $drops = [];
            if (Schema::hasColumn('tenants', 'attendance_security_settings')) {
                $drops[] = 'attendance_security_settings';
            }
            if (Schema::hasColumn('tenants', 'attendance_registered_device_hash')) {
                $drops[] = 'attendance_registered_device_hash';
            }
            if (Schema::hasColumn('tenants', 'onboarding_completed_at')) {
                $drops[] = 'onboarding_completed_at';
            }
            if (Schema::hasColumn('tenants', 'onboarding_dismissed_at')) {
                $drops[] = 'onboarding_dismissed_at';
            }
            if (! empty($drops)) {
                $table->dropColumn($drops);
            }
        });
    }
};
