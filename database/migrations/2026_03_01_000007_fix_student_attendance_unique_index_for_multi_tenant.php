<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('student_attendances', function (Blueprint $table) {
            $table->index('user_id', 'student_attendances_user_id_idx');
            $table->dropUnique('student_attendances_user_id_date_unique');
            $table->unique(['tenant_id', 'user_id', 'date'], 'student_attendances_tenant_user_date_unique');
        });
    }

    public function down(): void
    {
        Schema::table('student_attendances', function (Blueprint $table) {
            $table->dropUnique('student_attendances_tenant_user_date_unique');
            $table->unique(['user_id', 'date'], 'student_attendances_user_id_date_unique');
            $table->dropIndex('student_attendances_user_id_idx');
        });
    }
};
