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
        // Alter the enum column to include 'grid'
        // Using raw SQL is often best for ENUM modification in MySQL/MariaDB to avoid Doctrine DBAL issues with enums
        // But Laravel 10+ might handle specific native schema operations better.
        // For safety/portability, we can use DB::statement
        
        // Check which connection/table we are hitting.
        // The previous error implied 'store_configs' in the main DB context? 
        // Or tenant? The error said insert into store_configs ... SQLSTATE[01000]
        // Usually if it's tenant migration, we need to run it for tenants.
        // If store_configs is shared, run normally.
        
        // Based on "add_tenant_id_to_store_configs" migration, it seems it MIGHT be shared now?
        // But the original creation was in 'tenant' folder.
        
        // If it's a shared table in the main DB:
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
        if (Schema::hasTable('store_configs')) {
             DB::statement("ALTER TABLE store_configs MODIFY COLUMN layout_preference ENUM('minimal', 'showcase', 'catalog') DEFAULT 'minimal'");
        }
    }
};
