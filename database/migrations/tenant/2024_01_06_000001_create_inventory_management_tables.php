<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Warehouses/Branches
        Schema::create('warehouses', function (Blueprint $table) {
            $table->id();
            $table->string('tenant_id')->index();
            $table->string('name');
            $table->string('code'); // Unique per tenant
            $table->text('address')->nullable();
            $table->string('city')->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->foreignId('manager_id')->nullable()->constrained('users')->onDelete('set null');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            $table->unique(['tenant_id', 'code']);
        });

        // Suppliers
        Schema::create('suppliers', function (Blueprint $table) {
            $table->id();
            $table->string('tenant_id')->index();
            $table->string('name');
            $table->string('company_name')->nullable();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->text('address')->nullable();
            $table->string('city')->nullable();
            $table->string('country')->nullable();
            $table->string('tax_number')->nullable();
            $table->text('notes')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Purchase Orders
        Schema::create('purchase_orders', function (Blueprint $table) {
            $table->id();
            $table->string('tenant_id')->index();
            $table->string('po_number'); // Unique per tenant
            $table->foreignId('supplier_id')->constrained();
            $table->foreignId('warehouse_id')->constrained();
            $table->date('order_date');
            $table->date('expected_delivery_date')->nullable();
            $table->date('received_date')->nullable();
            $table->enum('status', ['draft', 'pending', 'approved', 'received', 'cancelled'])->default('draft');
            $table->decimal('subtotal', 12, 2);
            $table->decimal('tax', 12, 2)->default(0);
            $table->decimal('shipping', 12, 2)->default(0);
            $table->decimal('total', 12, 2);
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->constrained('users');
            $table->foreignId('approved_by')->nullable()->constrained('users');
            $table->timestamps();
            
            $table->unique(['tenant_id', 'po_number']);
        });

        Schema::create('purchase_order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('purchase_order_id')->constrained()->onDelete('cascade');
            $table->foreignId('product_id')->constrained();
            $table->foreignId('product_variant_id')->nullable()->constrained();
            $table->integer('quantity_ordered');
            $table->integer('quantity_received')->default(0);
            $table->decimal('unit_cost', 10, 2);
            $table->decimal('total', 10, 2);
            $table->timestamps();
        });

        // Stock Movements
        Schema::create('stock_movements', function (Blueprint $table) {
            $table->id();
            $table->string('tenant_id')->index();
            $table->foreignId('product_id')->constrained();
            $table->foreignId('product_variant_id')->nullable()->constrained();
            $table->foreignId('warehouse_id')->constrained();
            $table->enum('type', ['purchase', 'sale', 'adjustment', 'transfer', 'return']);
            $table->integer('quantity'); // positive for in, negative for out
            $table->integer('balance_after');
            $table->string('reference_type')->nullable(); // order, purchase_order, etc.
            $table->unsignedBigInteger('reference_id')->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->constrained('users');
            $table->timestamps();
        });

        // Warehouse Stock Levels
        Schema::create('warehouse_stock', function (Blueprint $table) {
            $table->id();
            // warehouse_id implies tenant, but linking explicitly can be redundant.
            // However, linking warehouse_id is enough if warehouse has tenant_id.
            $table->foreignId('warehouse_id')->constrained();
            $table->foreignId('product_id')->constrained();
            $table->foreignId('product_variant_id')->nullable()->constrained();
            $table->integer('quantity')->default(0);
            $table->timestamps();
            
            $table->unique(['warehouse_id', 'product_id', 'product_variant_id'], 'wh_stock_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('warehouse_stock');
        Schema::dropIfExists('stock_movements');
        Schema::dropIfExists('purchase_order_items');
        Schema::dropIfExists('purchase_orders');
        Schema::dropIfExists('suppliers');
        Schema::dropIfExists('warehouses');
    }
};
