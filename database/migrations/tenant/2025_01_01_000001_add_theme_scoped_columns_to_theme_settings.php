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
        if (Schema::hasTable('theme_settings')) {
            Schema::table('theme_settings', function (Blueprint $table) {
                // Add theme_slug for direct querying
                if (!Schema::hasColumn('theme_settings', 'theme_slug')) {
                    $table->string('theme_slug')->after('template_id')->nullable();
                }
                
                // Explicit theme configuration fields - Colors
                if (!Schema::hasColumn('theme_settings', 'primary_color')) {
                    $table->string('primary_color')->nullable()->after('theme_slug');
                }
                if (!Schema::hasColumn('theme_settings', 'secondary_color')) {
                    $table->string('secondary_color')->nullable();
                }
                if (!Schema::hasColumn('theme_settings', 'accent_color')) {
                    $table->string('accent_color')->nullable();
                }
                
                // Product card styling
                if (!Schema::hasColumn('theme_settings', 'product_card_style')) {
                    $table->enum('product_card_style', ['minimal', 'detailed', 'compact', 'featured'])
                        ->default('detailed')->after('accent_color');
                }
                
                // Layout configuration
                if (!Schema::hasColumn('theme_settings', 'layout_type')) {
                    $table->enum('layout_type', ['boxed', 'full-width', 'fluid'])
                        ->default('full-width')->after('product_card_style');
                }
                
                // Spacing configuration
                if (!Schema::hasColumn('theme_settings', 'spacing_scale')) {
                    $table->enum('spacing_scale', ['compact', 'normal', 'relaxed'])
                        ->default('normal')->after('layout_type');
                }
                
                // Additional theme-specific settings
                if (!Schema::hasColumn('theme_settings', 'border_radius')) {
                    $table->integer('border_radius')->default(8)->after('spacing_scale'); // in pixels
                }
                if (!Schema::hasColumn('theme_settings', 'show_shadows')) {
                    $table->boolean('show_shadows')->default(true);
                }
                if (!Schema::hasColumn('theme_settings', 'enable_animations')) {
                    $table->boolean('enable_animations')->default(true);
                }
            });
        }

        // Populate theme_slug from template relationship
        if (Schema::hasTable('theme_settings') && Schema::hasTable('storefront_templates')) {
            DB::statement('
                UPDATE theme_settings 
                SET theme_slug = (
                    SELECT slug 
                    FROM storefront_templates 
                    WHERE storefront_templates.id = theme_settings.template_id
                )
                WHERE theme_slug IS NULL AND EXISTS (
                    SELECT 1 
                    FROM storefront_templates 
                    WHERE storefront_templates.id = theme_settings.template_id
                )
            ');
        }

        // Make theme_slug required and unique after population
        if (Schema::hasTable('theme_settings') && Schema::hasColumn('theme_settings', 'theme_slug')) {
            // Check if unique index already exists
            if (!Schema::hasIndex('theme_settings', 'theme_settings_theme_slug_unique')) {
                Schema::table('theme_settings', function (Blueprint $table) {
                    $table->unique('theme_slug', 'theme_settings_theme_slug_unique');
                });
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('theme_settings', function (Blueprint $table) {
            // Drop unique constraint first
            $table->dropUnique(['theme_slug']);
            
            // Drop all added columns
            $table->dropColumn([
                'theme_slug',
                'primary_color',
                'secondary_color',
                'accent_color',
                'product_card_style',
                'layout_type',
                'spacing_scale',
                'border_radius',
                'show_shadows',
                'enable_animations',
            ]);
        });
    }
};
