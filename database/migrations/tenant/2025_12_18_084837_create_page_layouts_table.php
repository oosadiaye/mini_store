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
        if (!Schema::hasTable('page_layouts')) {
            Schema::create('page_layouts', function (Blueprint $table) {
                $table->id();
                $table->string('page_name'); // 'home', 'product', 'category', etc.
                $table->json('sections'); // Ordered array of section configurations
                $table->boolean('is_active')->default(true);
                $table->timestamps();
                
                $table->unique('page_name');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('page_layouts');
    }
};
