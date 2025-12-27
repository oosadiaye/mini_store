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
        if (Schema::hasTable('purchase_orders') && !Schema::hasColumn('purchase_orders', 'billed_status')) {
            Schema::table('purchase_orders', function (Blueprint $table) {
                $table->string('billed_status')->default('unbilled')->after('status');
            });
        }
        
        // Also verify invoice_number while we are here, as they were in the same migration
        if (Schema::hasTable('purchase_orders') && !Schema::hasColumn('purchase_orders', 'invoice_number')) {
            Schema::table('purchase_orders', function (Blueprint $table) {
                $table->string('invoice_number')->nullable()->after('po_number');
            });
        }
         // Also verify discount while we are here
        if (Schema::hasTable('purchase_orders') && !Schema::hasColumn('purchase_orders', 'discount')) {
            Schema::table('purchase_orders', function (Blueprint $table) {
                $table->decimal('discount', 12, 2)->default(0)->after('subtotal');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('purchase_orders')) {
            Schema::table('purchase_orders', function (Blueprint $table) {
                // We typically won't drop in a fix migration down to avoid data loss if rolled back
                // unless we are sure. But standard practice is to reverse changes.
                if (Schema::hasColumn('purchase_orders', 'billed_status')) {
                    $table->dropColumn('billed_status');
                }
            });
        }
    }
};
