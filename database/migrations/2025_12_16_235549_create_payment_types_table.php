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
        Schema::create('payment_types', function (Blueprint $table) {
            $table->id();
            $table->string('tenant_id');
            $table->foreign('tenant_id')->references('id')->on('tenants')->onDelete('cascade');
            $table->string('name');
            $table->enum('type', ['cash', 'bank']);
            $table->json('bank_details')->nullable(); // {bank_name, account_number, account_name}
            $table->boolean('is_active')->default(true);
            $table->foreignId('gl_account_id')->constrained('chart_of_accounts')->onDelete('cascade');
            $table->string('gateway_provider')->nullable(); // opay, moniepoint, paystack, flutterwave
            $table->boolean('require_gateway')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_types');
    }
};
