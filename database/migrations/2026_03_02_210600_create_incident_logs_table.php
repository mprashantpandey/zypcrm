<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (Schema::hasTable('incident_logs')) {
            return;
        }

        Schema::create('incident_logs', function (Blueprint $table) {
            $table->id();
            $table->string('level', 20)->default('info'); // info, warning, error
            $table->string('category', 50)->default('system');
            $table->string('title');
            $table->text('message')->nullable();
            $table->json('meta')->nullable();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('occurred_at')->nullable();
            $table->timestamps();

            $table->index(['level', 'created_at']);
            $table->index(['category', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('incident_logs');
    }
};
