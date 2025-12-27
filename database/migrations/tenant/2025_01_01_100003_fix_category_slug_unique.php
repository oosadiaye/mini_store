<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            // Drop the existing unique index on slug
            $table->dropUnique('slug');
            
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
