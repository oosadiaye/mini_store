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
        Schema::table('page_layouts', function (Blueprint $table) {
            // Drop the existing unique index (handle varied names robustly)
            try {
                $table->dropUnique(['page_name']);
            } catch (\Exception $e) {
                try {
                    $table->dropUnique('page_name');
                } catch (\Exception $ex) {
                    // Ignore if not found
                }
            }
            $table->unique(['page_name', 'template_id']);
        });
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
