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
        Schema::create('tax_codes', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique(); // e.g., VAT5, VAT7.5
            $table->string('name'); // e.g., "VAT 5%", "Sales Tax 7.5%"
            $table->decimal('rate', 5, 2); // Tax rate percentage (e.g., 5.00, 7.50)
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tax_codes');
    }
};
