<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private array $tables = [
        'employees',
        'employee_shifts',
        'customer_service_records',
        'service_requests',
    ];

    public function up(): void
    {
        foreach ($this->tables as $tableName) {
            if (!Schema::hasColumn($tableName, 'branch_code')) {
                Schema::table($tableName, function (Blueprint $table) {
                    // Nullable to preserve compatibility with existing deployments/data.
                    // New writes always set this value from the authenticated session.
                    $table->string('branch_code', 50)->nullable();
                    $table->index('branch_code');
                });
            }
        }
    }

    public function down(): void
    {
        foreach (array_reverse($this->tables) as $tableName) {
            if (Schema::hasColumn($tableName, 'branch_code')) {
                Schema::table($tableName, function (Blueprint $table) {
                    $table->dropIndex(['branch_code']);
                    $table->dropColumn('branch_code');
                });
            }
        }
    }
};
