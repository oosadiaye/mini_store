<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $tables = ['orders', 'incomes', 'expenses', 'journal_entries'];

        foreach ($tables as $tableName) {
            if (Schema::hasTable($tableName) && !Schema::hasColumn($tableName, 'warehouse_id')) {
                Schema::table($tableName, function (Blueprint $table) {
                    $table->foreignId('warehouse_id')
                          ->nullable()
                          ->after('id')
                          ->constrained('warehouses')
                          ->onDelete('set null');
                });
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $tables = ['orders', 'incomes', 'expenses', 'journal_entries'];

        foreach ($tables as $tableName) {
            if (Schema::hasTable($tableName) && Schema::hasColumn($tableName, 'warehouse_id')) {
                Schema::table($tableName, function (Blueprint $table) {
                    $table->dropForeign(['warehouse_id']);
                    $table->dropColumn('warehouse_id');
                });
            }
        }
    }
};
