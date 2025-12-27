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
        Schema::table('theme_settings', function (Blueprint $table) {
            // Check if column exists before modifying, or add it if missing
            if (Schema::hasColumn('theme_settings', 'tenant_id')) {
                // Change to string to support slug-based IDs
                $table->string('tenant_id', 191)->change();
            } else {
                $table->string('tenant_id', 191)->nullable()->after('is_active');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Reverting this is risky without knowing the original state perfectly, 
        // but generally we wouldn't want to revert a type fix.
        // Leaving empty or reverting to integer if absolutely necessary.
    }
};
