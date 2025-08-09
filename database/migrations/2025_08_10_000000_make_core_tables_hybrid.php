<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        $tables = ['projects','tasks','categories'];

        foreach ($tables as $table) {
            if (! Schema::hasTable($table)) continue;

            Schema::table($table, function (Blueprint $t) use ($table) {
                if (Schema::hasColumn($table, 'tenant_id')) {
                    $t->uuid('tenant_id')->nullable()->change();
                }
                if (! Schema::hasColumn($table, 'user_id')) {
                    $t->uuid('user_id')->nullable()->after('tenant_id')->index();
                }
            });
        }

        $constraints = [
            'projects' => 'projects_tenant_or_user_chk',
            'tasks' => 'tasks_tenant_or_user_chk',
            'categories' => 'categories_tenant_or_user_chk',
        ];

        foreach ($constraints as $table => $name) {
            if (! Schema::hasTable($table)) continue;
            try {
                DB::statement(<<<SQL
                    ALTER TABLE {$table}
                    ADD CONSTRAINT {$name}
                    CHECK ( (tenant_id IS NOT NULL AND user_id IS NULL) OR (tenant_id IS NULL AND user_id IS NOT NULL) )
                SQL);
            } catch (Throwable $e) {
                // Ignore if unsupported / already exists
            }
        }
    }

    public function down(): void
    {
        $constraints = [
            'projects' => 'projects_tenant_or_user_chk',
            'tasks' => 'tasks_tenant_or_user_chk',
            'categories' => 'categories_tenant_or_user_chk',
        ];
        foreach ($constraints as $table => $name) {
            try { DB::statement("ALTER TABLE {$table} DROP CONSTRAINT {$name}"); } catch (Throwable $e) {}
        }
    }
};
