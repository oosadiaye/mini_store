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
        // Attempt to drop 'page_name' index (array syntax for inferred name)
        try {
            Schema::table('page_layouts', function (Blueprint $table) {
                $table->dropUnique(['page_name']);
            });
        } catch (\Throwable $e) {}

        // Attempt to drop 'page_name' index (explicit string name)
        try {
            Schema::table('page_layouts', function (Blueprint $table) {
                $table->dropUnique('page_name');
            });
        } catch (\Throwable $e) {}

        // Add the new unique index if it doesn't exist
        if (!Schema::hasIndex('page_layouts', 'page_layouts_page_name_template_id_unique')) {
            Schema::table('page_layouts', function (Blueprint $table) {
                $table->unique(['page_name', 'template_id']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('page_layouts', function (Blueprint $table) {
            $table->dropUnique(['page_name', 'template_id']);
            $table->unique(['page_name']);
        });
    }
};
