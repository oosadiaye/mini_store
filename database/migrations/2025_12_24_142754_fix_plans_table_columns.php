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
        Schema::table('plans', function (Blueprint $table) {
            // Add duration_days if missing
            if (!Schema::hasColumn('plans', 'duration_days')) {
                $table->integer('duration_days')->default(30)->after('price');
            }

            // Remove obsolete columns from old schema
            if (Schema::hasColumn('plans', 'duration_months')) {
                $table->dropColumn('duration_months');
            }
            if (Schema::hasColumn('plans', 'slug')) {
                // Drop index first for SQLite compatibility
                $table->dropUnique(['slug']); // standard laravel index name assumption: plans_slug_unique
                $table->dropColumn('slug');
            }
            if (Schema::hasColumn('plans', 'currency')) {
                $table->dropColumn('currency');
            }
            if (Schema::hasColumn('plans', 'sort_order')) {
                $table->dropColumn('sort_order');
            }
            
            // Ensure caps exists just in case
            if (!Schema::hasColumn('plans', 'caps')) {
                $table->json('caps')->nullable()->after('features');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('plans', function (Blueprint $table) {
            // Revert changes is complicated because we dropped data
            // We'll just add columns back primarily
            if (!Schema::hasColumn('plans', 'duration_months')) {
                $table->integer('duration_months')->default(1);
            }
             if (Schema::hasColumn('plans', 'duration_days')) {
                $table->dropColumn('duration_days');
            }
        });
    }
};
