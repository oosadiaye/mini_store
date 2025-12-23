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
        Schema::table('purchase_orders', function (Blueprint $table) {
            $table->decimal('discount', 12, 2)->default(0)->after('subtotal');
            $table->string('billed_status')->default('unbilled')->after('status'); // unbilled, partial, billed
            $table->string('invoice_number')->nullable()->after('po_number');
        });
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
