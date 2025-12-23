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
        Schema::table('theme_settings', function (Blueprint $table) {
            // Add theme_slug for direct querying
            $table->string('theme_slug')->after('template_id')->nullable();
            
            // Explicit theme configuration fields - Colors
            $table->string('primary_color')->nullable()->after('theme_slug');
            $table->string('secondary_color')->nullable();
            $table->string('accent_color')->nullable();
            
            // Product card styling
            $table->enum('product_card_style', ['minimal', 'detailed', 'compact', 'featured'])
                ->default('detailed')->after('accent_color');
            
            // Layout configuration
            $table->enum('layout_type', ['boxed', 'full-width', 'fluid'])
                ->default('full-width')->after('product_card_style');
            
            // Spacing configuration
            $table->enum('spacing_scale', ['compact', 'normal', 'relaxed'])
                ->default('normal')->after('layout_type');
            
            // Additional theme-specific settings
            $table->integer('border_radius')->default(8)->after('spacing_scale'); // in pixels
            $table->boolean('show_shadows')->default(true);
            $table->boolean('enable_animations')->default(true);
        });

        // Populate theme_slug from template relationship
        DB::statement('
            UPDATE theme_settings 
            INNER JOIN storefront_templates ON theme_settings.template_id = storefront_templates.id 
            SET theme_settings.theme_slug = storefront_templates.slug
        ');

        // Make theme_slug required and unique after population
        Schema::table('theme_settings', function (Blueprint $table) {
            $table->string('theme_slug')->nullable(false)->change();
            $table->unique('theme_slug');
        });
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
