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
        Schema::table('tax_codes', function (Blueprint $table) {
            $table->enum('type', ['sales', 'purchase', 'both'])->default('both')->after('rate');
            $table->string('sales_tax_gl_account')->nullable()->after('type');
            $table->string('purchase_tax_gl_account')->nullable()->after('sales_tax_gl_account');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tax_codes', function (Blueprint $table) {
            $table->dropColumn(['type', 'sales_tax_gl_account', 'purchase_tax_gl_account']);
        });
    }
};
