<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::hasTable('purchase_orders')) {
            Schema::table('purchase_orders', function (Blueprint $table) {
                // Change ENUM to String to support 'ordered' and others flexible
                // We use change() but sometimes ENUM change requires DB::statement
                // Let's try change first, but standard for ENUM->String is fine usually.
                $table->string('status')->default('draft')->change();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('purchase_orders')) {
             Schema::table('purchase_orders', function (Blueprint $table) {
                // Revert to ENUM if needed (risky if data contains other values)
                // $table->enum('status', ['draft', 'pending', 'approved', 'received', 'cancelled'])->default('draft')->change();
             });
        }
    }
};
