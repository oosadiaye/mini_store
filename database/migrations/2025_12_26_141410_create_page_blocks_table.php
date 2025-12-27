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
        Schema::create('page_blocks', function (Blueprint $table) {
            $table->id();
            $table->string('tenant_id')->index(); // Tenant isolation
            $table->string('page'); // 'home', 'about', etc.
            $table->string('block_id')->unique(); // 'home_hero', 'home_features', etc.
            $table->string('block_type'); // 'hero', 'features', 'testimonials', etc.
            $table->json('content'); // Block-specific content fields
            $table->json('settings')->nullable(); // Visual settings
            $table->integer('order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            $table->index(['page', 'order']);
            $table->index('block_id');
            
            // Foreign key for tenant
            $table->foreign('tenant_id')->references('id')->on('tenants')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('page_blocks');
    }
};
