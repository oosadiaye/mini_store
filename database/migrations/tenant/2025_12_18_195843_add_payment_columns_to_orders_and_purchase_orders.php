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
            if (!Schema::hasColumn('purchase_orders', 'payment_status')) {
                $table->string('payment_status')->default('pending'); // pending, partial, paid
            }
            if (!Schema::hasColumn('purchase_orders', 'amount_paid')) {
                $table->decimal('amount_paid', 10, 2)->default(0);
            }
        });

        Schema::table('orders', function (Blueprint $table) {
            if (!Schema::hasColumn('orders', 'amount_paid')) {
                $table->decimal('amount_paid', 10, 2)->default(0)->after('total');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn('amount_paid');
        });

        Schema::table('purchase_orders', function (Blueprint $table) {
            $table->dropColumn(['payment_status', 'amount_paid']);
        });
    }
};
