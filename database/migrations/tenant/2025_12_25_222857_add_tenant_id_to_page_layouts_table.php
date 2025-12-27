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
        if (Schema::hasTable('page_layouts')) {
            // Add tenant_id if not exists
            Schema::table('page_layouts', function (Blueprint $table) {
                if (!Schema::hasColumn('page_layouts', 'tenant_id')) {
                    $table->string('tenant_id')->after('id')->nullable()->index(); // Make nullable first to populate? No, default is difficult. Make nullable for now.
                    // Actually, if we make it nullable, unique constraint works, but we ideally want it populated.
                }
            });
            
            // Populate tenant_id for existing records?
            // Assuming we can't easily, let's just proceed with schema.
            // But strict mode might fail if we set not null.
            // Update: Make it nullable first, then we can fix data later if needed.

            // Modify parameters for tenant_id to be nullable just in case of existing data
             Schema::table('page_layouts', function (Blueprint $table) {
                if (Schema::hasColumn('page_layouts', 'tenant_id')) {
                    $table->string('tenant_id')->nullable()->change();
                }
            });

            // Updating unique constraint to include tenant_id
            // We use a raw statement or separate blocks to try dropping indices
            
            try {
                Schema::table('page_layouts', function (Blueprint $table) {
                    $table->dropUnique('page_layouts_page_name_unique');
                });
            } catch (\Throwable $e) {}

            try {
                Schema::table('page_layouts', function (Blueprint $table) {
                    $table->dropUnique(['page_name', 'template_id']);
                });
            } catch (\Throwable $e) {}

            Schema::table('page_layouts', function (Blueprint $table) {
                 // Add new unique index
                 // We need to name it explicitly to avoid too long names if auto-generated? Laravel auto names are usually fine.
                 $table->unique(['tenant_id', 'page_name', 'template_id']);
            });
        }
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
