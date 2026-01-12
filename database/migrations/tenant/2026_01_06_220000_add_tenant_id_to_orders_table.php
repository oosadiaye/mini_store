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
        if (Schema::hasTable('orders') && !Schema::hasColumn('orders', 'tenant_id')) {
            Schema::table('orders', function (Blueprint $table) {
                // Add tenant_id - nullable first to avoid issues with existing data, 
                // but for new orders it should be required.
                // Since this is tenant-specific migration loaded into single DB, 
                // we assume key is string (from Tenant model).
                $table->string('tenant_id')->after('id')->nullable();
                $table->index('tenant_id');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('orders') && Schema::hasColumn('orders', 'tenant_id')) {
            Schema::table('orders', function (Blueprint $table) {
                $table->dropColumn('tenant_id');
            });
        }
    }
};
