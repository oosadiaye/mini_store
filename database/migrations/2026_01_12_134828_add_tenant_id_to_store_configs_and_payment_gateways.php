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
        if (Schema::hasTable('store_configs') && !Schema::hasColumn('store_configs', 'tenant_id')) {
            Schema::table('store_configs', function (Blueprint $table) {
                $table->string('tenant_id')->nullable()->after('id')->index();
            });
        }

        if (Schema::hasTable('payment_gateways') && !Schema::hasColumn('payment_gateways', 'tenant_id')) {
            Schema::table('payment_gateways', function (Blueprint $table) {
                $table->string('tenant_id')->nullable()->after('id')->index();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('store_configs') && Schema::hasColumn('store_configs', 'tenant_id')) {
            Schema::table('store_configs', function (Blueprint $table) {
                $table->dropColumn('tenant_id');
            });
        }

        if (Schema::hasTable('payment_gateways') && Schema::hasColumn('payment_gateways', 'tenant_id')) {
            Schema::table('payment_gateways', function (Blueprint $table) {
                $table->dropColumn('tenant_id');
            });
        }
    }
};
