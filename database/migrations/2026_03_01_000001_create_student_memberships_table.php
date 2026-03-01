<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('student_memberships', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->string('status')->default('active');
            $table->timestamp('joined_at')->nullable();
            $table->timestamps();

            $table->unique(['user_id', 'tenant_id']);
            $table->index(['tenant_id', 'status']);
        });

        $now = now();
        $rows = DB::table('users')
            ->where('role', 'student')
            ->whereNotNull('tenant_id')
            ->get(['id as user_id', 'tenant_id', 'created_at']);

        foreach ($rows as $row) {
            DB::table('student_memberships')->updateOrInsert(
                ['user_id' => $row->user_id, 'tenant_id' => $row->tenant_id],
                [
                    'status' => 'active',
                    'joined_at' => $row->created_at ?? $now,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]
            );
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('student_memberships');
    }
};

