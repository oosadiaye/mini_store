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
        Schema::table('order_items', function (Blueprint $table) {
            $table->decimal('tax_amount', 10, 2)->default(0)->after('price');
            $table->foreignId('tax_code_id')->nullable()->after('tax_amount')->constrained('tax_codes')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            $table->dropForeign(['tax_code_id']);
            $table->dropColumn(['tax_amount', 'tax_code_id']);
        });
    }
};
