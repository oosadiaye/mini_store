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
        if (Schema::hasTable('purchase_orders')) {
            Schema::table('purchase_orders', function (Blueprint $table) {
                if (!Schema::hasColumn('purchase_orders', 'discount')) {
                    $table->decimal('discount', 12, 2)->default(0)->after('subtotal');
                }
                if (!Schema::hasColumn('purchase_orders', 'billed_status')) {
                    $table->string('billed_status')->default('unbilled')->after('status'); // unbilled, partial, billed
                }
                if (!Schema::hasColumn('purchase_orders', 'invoice_number')) {
                    $table->string('invoice_number')->nullable()->after('po_number');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('purchase_orders', function (Blueprint $table) {
            $table->dropColumn(['discount', 'billed_status', 'invoice_number']);
        });
    }
};
