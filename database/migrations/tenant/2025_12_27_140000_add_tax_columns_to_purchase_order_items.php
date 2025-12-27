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
        if (Schema::hasTable('purchase_order_items')) {
            Schema::table('purchase_order_items', function (Blueprint $table) {
                if (!Schema::hasColumn('purchase_order_items', 'tax_amount')) {
                     $table->decimal('tax_amount', 15, 2)->default(0)->after('unit_cost');
                }
                if (!Schema::hasColumn('purchase_order_items', 'tax_code_id')) {
                    $table->foreignId('tax_code_id')->nullable()->after('tax_amount')->constrained('tax_codes')->nullOnDelete();
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('purchase_order_items')) {
            Schema::table('purchase_order_items', function (Blueprint $table) {
                $table->dropForeign(['tax_code_id']);
                $table->dropColumn(['tax_amount', 'tax_code_id']);
            });
        }
    }
};
