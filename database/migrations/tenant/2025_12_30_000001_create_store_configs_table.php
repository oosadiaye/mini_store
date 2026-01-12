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
        Schema::create('store_configs', function (Blueprint $table) {
            $table->id();
            $table->string('store_name')->nullable();
            $table->string('logo_path')->nullable();
            $table->string('brand_color')->default('#3b82f6'); // Default blue
            $table->string('industry')->nullable(); // Changed to string for flexibility
            $table->json('selected_categories')->nullable();
            $table->string('layout_preference')->default('minimal'); // Changed from enum to string to support 'grid'
            $table->boolean('is_completed')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('store_configs');
    }
};
