<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            if (!Schema::hasColumn('categories', 'is_visible_online')) {
                $table->boolean('is_visible_online')->default(true)->after('is_active');
            }
            if (!Schema::hasColumn('categories', 'public_display_name')) {
                $table->string('public_display_name')->nullable()->after('name');
            }
            // sort_order might already exist from previous migration check, but ensuring it.
            // Check based on 2024_01_02_000001_create_categories_table.php content which had sort_order.
            // If it exists, we skip or modify.
            // defined in create_categories: $table->integer('sort_order')->default(0);
        });
    }

    public function down(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->dropColumn(['is_visible_online', 'public_display_name']);
        });
    }
};
