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
        Schema::table('storefront_templates', function (Blueprint $table) {
            if (!Schema::hasColumn('storefront_templates', 'layout_data')) {
                $table->json('layout_data')->nullable()->after('default_settings');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('storefront_templates', function (Blueprint $table) {
            $table->dropColumn('layout_data');
        });
    }
};
