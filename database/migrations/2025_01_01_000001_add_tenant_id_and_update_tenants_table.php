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
        // Add tenant_id to users table (central database)
        if (!Schema::hasColumn('users', 'tenant_id')) {
            Schema::table('users', function (Blueprint $table) {
                $table->string('tenant_id')->nullable()->after('id');
                $table->index('tenant_id');
            });
        }

        // Update tenants table for new architecture
        Schema::table('tenants', function (Blueprint $table) {
            // Add slug for URL routing
            if (!Schema::hasColumn('tenants', 'slug')) {
                $table->string('slug')->unique()->after('id');
            }
            
            // Add is_active status
            if (!Schema::hasColumn('tenants', 'is_active')) {
                $table->boolean('is_active')->default(true)->after('subscription_ends_at');
            }
            
            // Add settings JSON field
            if (!Schema::hasColumn('tenants', 'settings')) {
                $table->json('settings')->nullable()->after('data');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['tenant_id']);
            $table->dropColumn('tenant_id');
        });

        Schema::table('tenants', function (Blueprint $table) {
            $table->dropColumn(['slug', 'is_active', 'settings']);
        });
    }
};
