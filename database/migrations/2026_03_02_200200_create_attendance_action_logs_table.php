<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (Schema::hasTable('attendance_action_logs')) {
            return;
        }

        Schema::create('attendance_action_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('operator_id')->nullable()->constrained('users')->nullOnDelete();
            $table->date('date');
            $table->string('action', 40);
            $table->string('status', 20)->nullable();
            $table->boolean('success')->default(true);
            $table->string('message')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->string('device_hash', 64)->nullable();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->json('meta')->nullable();
            $table->timestamps();

            $table->index(['tenant_id', 'date']);
            $table->index(['user_id', 'date']);
            $table->index(['operator_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attendance_action_logs');
    }
};
