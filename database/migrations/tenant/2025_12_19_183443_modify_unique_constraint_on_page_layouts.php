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
            $table->dropUnique('page_name'); // Explicitly drop the index named 'page_name'
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
