<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('tenants', function (Blueprint $table) {
            $table->string('public_slug')->nullable()->after('operating_hours');
            $table->text('public_description')->nullable()->after('public_slug');
            $table->boolean('public_page_enabled')->default(true)->after('public_description');
        });

        DB::table('tenants')->orderBy('id')->get(['id', 'name'])->each(function ($tenant): void {
            $base = Str::slug((string) $tenant->name);
            if ($base === '') {
                $base = 'library-'.$tenant->id;
            }

            $slug = $base;
            $counter = 1;
            while (DB::table('tenants')->where('public_slug', $slug)->where('id', '!=', $tenant->id)->exists()) {
                $slug = $base.'-'.$counter;
                $counter++;
            }

            DB::table('tenants')->where('id', $tenant->id)->update(['public_slug' => $slug]);
        });

        Schema::table('tenants', function (Blueprint $table) {
            $table->unique('public_slug');
        });
    }

    public function down(): void
    {
        Schema::table('tenants', function (Blueprint $table) {
            $table->dropUnique(['public_slug']);
            $table->dropColumn(['public_slug', 'public_description', 'public_page_enabled']);
        });
    }
};
