<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('renters')) {
            Schema::create('renters', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('email')->nullable();
                $table->string('phone')->nullable();
                $table->text('address')->nullable();
                $table->string('id_number')->nullable()->comment('ID/Passport number');
                
                // Contract Details
                $table->date('contract_start_date')->nullable();
                $table->date('contract_end_date')->nullable();
                $table->decimal('rental_amount', 15, 2)->default(0)->comment('Periodic rental amount');
                $table->enum('payment_frequency', ['daily', 'weekly', 'monthly', 'quarterly', 'yearly'])->default('monthly');
                $table->decimal('security_deposit', 15, 2)->default(0);
                
                // Status
                $table->enum('status', ['active', 'inactive', 'terminated'])->default('active');
                $table->text('notes')->nullable();
                
                // Multi-tenancy
                $table->string('tenant_id');
                
                $table->timestamps();
                $table->softDeletes();
                
                $table->index('tenant_id');
                $table->index('status');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('renters');
    }
};
