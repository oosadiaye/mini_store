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
        if (Schema::hasTable('store_configs')) {
             if (!Schema::hasColumn('store_configs', 'selected_categories')) {
                Schema::table('store_configs', function (Blueprint $table) {
                    $table->json('selected_categories')->nullable()->after('brand_color');
                });
             }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('store_configs') && Schema::hasColumn('store_configs', 'selected_categories')) {
            Schema::table('store_configs', function (Blueprint $table) {
                $table->dropColumn('selected_categories');
            });
        }
    }
};
