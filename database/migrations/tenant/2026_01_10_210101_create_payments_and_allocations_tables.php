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
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->string('tenant_id')->index();
            $table->string('entity_type'); // App\Models\Customer or App\Models\Supplier
            $table->unsignedBigInteger('entity_id');
            $table->decimal('amount', 15, 2);
            $table->decimal('unallocated_amount', 15, 2);
            $table->date('payment_date');
            $table->string('payment_method'); // cash, bank, etc.
            $table->string('reference')->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('journal_entry_id')->nullable()->constrained('journal_entries')->onDelete('set null');
            $table->timestamps();

            $table->index(['entity_type', 'entity_id']);
        });

        Schema::create('payment_allocations', function (Blueprint $table) {
            $table->id();
            $table->string('tenant_id')->index();
            $table->foreignId('payment_id')->constrained('payments')->onDelete('cascade');
            $table->string('allocatable_type'); // App\Models\Order or App\Models\SupplierInvoice
            $table->unsignedBigInteger('allocatable_id');
            $table->decimal('amount', 15, 2);
            $table->timestamps();

            $table->index(['allocatable_type', 'allocatable_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_allocations');
        Schema::dropIfExists('payments');
    }
};
