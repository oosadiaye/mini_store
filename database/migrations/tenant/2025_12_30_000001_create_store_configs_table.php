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
            $table->enum('industry', ['fashion', 'electronics', 'grocery'])->nullable();
            $table->json('selected_categories')->nullable();
            $table->enum('layout_preference', ['minimal', 'showcase', 'catalog'])->default('minimal');
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
