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
        Schema::create('custom_domain_requests', function (Blueprint $table) {
            $table->id();
            $table->string('tenant_id');
            $table->string('domain')->unique();
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->enum('ssl_status', ['pending', 'active', 'failed'])->default('pending');
            $table->text('dns_instructions')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->unsignedBigInteger('approved_by')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->timestamps();
            
            $table->foreign('tenant_id')->references('id')->on('tenants')->onDelete('cascade');
            $table->foreign('approved_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('custom_domain_requests');
    }
};
