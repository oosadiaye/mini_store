<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Payment Gateways Configuration
        Schema::create('payment_gateways', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique(); // opay, paystack, flutterwave
            $table->string('display_name');
            $table->boolean('is_active')->default(false);
            $table->json('config')->nullable(); // API keys, secrets, etc.
            $table->timestamps();
        });

        // Subscription Payments
        Schema::create('subscription_payments', function (Blueprint $table) {
            $table->id();
            $table->string('tenant_id');
            $table->foreignId('plan_id')->constrained('plans')->onDelete('cascade');
            $table->decimal('amount', 10, 2);
            $table->string('payment_method'); // gateway name or 'manual'
            $table->enum('status', ['pending', 'completed', 'failed', 'rejected'])->default('pending');
            $table->string('payment_proof')->nullable(); // File path for manual payments
            $table->string('transaction_reference')->nullable();
            $table->json('metadata')->nullable();
            $table->unsignedBigInteger('approved_by')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();

            $table->foreign('tenant_id')->references('id')->on('tenants')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('subscription_payments');
        Schema::dropIfExists('payment_gateways');
    }
};
