<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::hasTable('store_configs')) {
             // Change to VARCHAR to avoid ENUM strict mode issues and allow flexibility.
             DB::statement("ALTER TABLE store_configs MODIFY COLUMN layout_preference VARCHAR(255) DEFAULT 'minimal'");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No down needed or revert to enum if you really want, but string is safe.
    }
};
