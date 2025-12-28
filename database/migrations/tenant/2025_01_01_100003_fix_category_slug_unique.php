<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            // Drop the existing unique index on slug (tries 'categories_slug_unique')
            try {
                $table->dropUnique(['slug']);
            } catch (\Exception $e) {
                // If index doesn't exist or is named differently, try explicit default name
                try {
                     $table->dropUnique('categories_slug_unique');
                } catch (\Exception $ex) {
                    // Ignore if it doesn't exist
                }
            }
            
            // Add composite unique index
            $table->unique(['tenant_id', 'slug']);
        });
    }

    public function down(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->dropUnique(['tenant_id', 'slug']);
            $table->unique('slug');
        });
    }
};
