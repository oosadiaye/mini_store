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
        $tables = [
            'purchase_order_items',
            'purchase_returns',
            'purchase_return_items',
            'order_returns',
            'order_return_items'
        ];

        foreach ($tables as $table) {
            Schema::table($table, function (Blueprint $table) {
                if (!Schema::hasColumn($table->getTable(), 'tenant_id')) {
                    $table->string('tenant_id')->after('id')->nullable()->index();
                    // We set it to nullable first to avoid errors on existing data if any
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $tables = [
            'purchase_order_items',
            'purchase_returns',
            'purchase_return_items',
            'order_returns',
            'order_return_items'
        ];

        foreach ($tables as $table) {
            Schema::table($table, function (Blueprint $table) {
                if (Schema::hasColumn($table->getTable(), 'tenant_id')) {
                    $table->dropColumn('tenant_id');
                }
            });
        }
    }
};
