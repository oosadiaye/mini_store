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
        // Redundant: tenant_id is now added in the create_page_layouts_table migration manually.
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('page_layouts', function (Blueprint $table) {
             // Reverting is complex due to data integrity, but simpler for dev
             $table->dropForeign(['tenant_id']);
             $table->dropColumn('tenant_id');
             
             // Re-add old unique (imperfect)
             $table->unique(['page_name', 'template_id']);
        });
    }
};
