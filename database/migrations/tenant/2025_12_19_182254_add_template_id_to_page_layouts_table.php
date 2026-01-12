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
        if (Schema::hasTable('page_layouts')) {
            Schema::table('page_layouts', function (Blueprint $table) {
                if (!Schema::hasColumn('page_layouts', 'template_id')) {
                    $table->foreignId('template_id')->nullable()->after('page_name')->constrained('storefront_templates')->onDelete('cascade');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('page_layouts', function (Blueprint $table) {
            $table->dropForeign(['template_id']);
            $table->dropColumn('template_id');
        });
    }
};
