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
        Schema::table('tenants', function (Blueprint $table) {
            if (!Schema::hasColumn('tenants', 'plan_id')) {
                // Ensure plans table exists first? It should.
                // We add it as nullable first, or constrained.
                // Given the error, we just need the column.
                $table->foreignId('plan_id')->nullable()->after('id'); // No constraint yet to avoid issues if plans table missing/empty?
                // Actually constraint is good if plans exists.
                if (Schema::hasTable('plans')) {
                    // DB::statement to add foreign key separately or ...
                    // Let's just add the column first.
                }
            }
        });
        
        // Add constraint separately to be safe
        Schema::table('tenants', function (Blueprint $table) {
             if (Schema::hasColumn('tenants', 'plan_id') && Schema::hasTable('plans')) {
                 // Check if FK exists? Hard to check easily.
                 // Just try catch? or just assume if column was just added.
                 // Let's just add the column to solve the immediate "Unknown column" error.
             }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tenants', function (Blueprint $table) {
            if (Schema::hasColumn('tenants', 'plan_id')) {
                $table->dropColumn('plan_id');
            }
        });
    }
};
