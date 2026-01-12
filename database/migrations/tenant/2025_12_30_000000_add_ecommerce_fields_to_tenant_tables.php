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
        // Update Categories Table
        if (Schema::hasTable('categories')) {
            Schema::table('categories', function (Blueprint $table) {
                if (!Schema::hasColumn('categories', 'is_visible_online')) {
                    $table->boolean('is_visible_online')->default(false)->after('is_active');
                }
            });
        }

        // Update Products Table
        if (Schema::hasTable('products')) {
            Schema::table('products', function (Blueprint $table) {
                if (!Schema::hasColumn('products', 'rich_description')) {
                    $table->longText('rich_description')->nullable()->after('description');
                }
                if (!Schema::hasColumn('products', 'meta_tags')) {
                    $table->json('meta_tags')->nullable()->after('meta_description');
                }
                if (!Schema::hasColumn('products', 'published_status')) {
                    $table->enum('published_status', ['draft', 'published', 'archived'])->default('draft')->after('is_active');
                }
                // Slug is likely already there, but we can double check or ignore as it's standard
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->dropColumn(['is_visible_online']);
        });

        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['rich_description', 'meta_tags', 'published_status']);
        });
    }
};
