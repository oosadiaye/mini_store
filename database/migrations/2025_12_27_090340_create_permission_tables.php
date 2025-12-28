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
        // Duplicate migration.
        // Permissions tables are already created by tenant/2025_12_18_000000_create_permission_tables.php
    }

    public function down(): void
    {
    }
};
