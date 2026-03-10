<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('student_attendances', function (Blueprint $table) {
            if (! Schema::hasColumn('student_attendances', 'action_ip')) {
                $table->string('action_ip', 45)->nullable()->after('check_out');
            }
            if (! Schema::hasColumn('student_attendances', 'action_device')) {
                $table->string('action_device', 64)->nullable()->after('action_ip');
            }
            if (! Schema::hasColumn('student_attendances', 'action_latitude')) {
                $table->decimal('action_latitude', 10, 7)->nullable()->after('action_device');
            }
            if (! Schema::hasColumn('student_attendances', 'action_longitude')) {
                $table->decimal('action_longitude', 10, 7)->nullable()->after('action_latitude');
            }
            if (! Schema::hasColumn('student_attendances', 'anomaly_flags')) {
                $table->json('anomaly_flags')->nullable()->after('action_longitude');
            }
        });
    }

    public function down(): void
    {
        Schema::table('student_attendances', function (Blueprint $table) {
            $drops = [];
            foreach (['action_ip', 'action_device', 'action_latitude', 'action_longitude', 'anomaly_flags'] as $column) {
                if (Schema::hasColumn('student_attendances', $column)) {
                    $drops[] = $column;
                }
            }
            if (! empty($drops)) {
                $table->dropColumn($drops);
            }
        });
    }
};
