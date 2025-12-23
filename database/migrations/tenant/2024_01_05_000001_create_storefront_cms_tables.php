<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Storefront Templates
        Schema::create('storefront_templates', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('thumbnail')->nullable();
            $table->json('default_settings')->nullable();
            $table->boolean('is_premium')->default(false);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Theme Settings
        Schema::create('theme_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('template_id')->constrained('storefront_templates');
            $table->json('colors')->nullable(); // primary, secondary, accent, etc.
            $table->json('fonts')->nullable(); // heading, body fonts
            $table->json('layout_settings')->nullable();
            $table->json('custom_css')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Pages (CMS)
        Schema::create('pages', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->longText('content')->nullable();
            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();
            $table->boolean('is_published')->default(false);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        // Page Sections (Hero, Banner, etc.)
        Schema::create('page_sections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('page_id')->constrained()->onDelete('cascade');
            $table->string('section_type'); // hero, banner, product_slider, featured_products, etc.
            $table->string('title')->nullable();
            $table->text('content')->nullable();
            $table->json('settings')->nullable(); // images, links, colors, etc.
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Banners
        Schema::create('banners', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('image');
            $table->string('link')->nullable();
            $table->string('button_text')->nullable();
            $table->enum('position', ['home_hero', 'home_top', 'home_middle', 'sidebar', 'footer']);
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->timestamps();
        });

        // Product Collections (for featured, recently viewed, etc.)
        Schema::create('product_collections', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->enum('type', ['manual', 'auto_featured', 'auto_new', 'auto_bestseller']);
            $table->json('rules')->nullable(); // for automatic collections
            $table->integer('sort_order')->default(0);
            $table->boolean('show_on_homepage')->default(false);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('collection_products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('collection_id')->constrained('product_collections')->onDelete('cascade');
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        // Recently Viewed Products (session-based tracking)
        Schema::create('product_views', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained();
            $table->foreignId('customer_id')->nullable()->constrained();
            $table->string('session_id')->nullable();
            $table->ipAddress('ip_address')->nullable();
            $table->timestamps();
            
            $table->index(['customer_id', 'created_at']);
            $table->index(['session_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_views');
        Schema::dropIfExists('collection_products');
        Schema::dropIfExists('product_collections');
        Schema::dropIfExists('banners');
        Schema::dropIfExists('page_sections');
        Schema::dropIfExists('pages');
        Schema::dropIfExists('theme_settings');
        Schema::dropIfExists('storefront_templates');
    }
};
