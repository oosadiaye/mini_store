<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Chart of Accounts
        Schema::create('chart_of_accounts', function (Blueprint $table) {
            $table->id();
            $table->string('account_code')->unique();
            $table->string('account_name');
            $table->enum('account_type', ['asset', 'liability', 'equity', 'revenue', 'expense']);
            $table->foreignId('parent_id')->nullable()->constrained('chart_of_accounts')->onDelete('cascade');
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Income Transactions
        Schema::create('incomes', function (Blueprint $table) {
            $table->id();
            $table->string('reference_number')->unique();
            $table->date('transaction_date');
            $table->foreignId('account_id')->constrained('chart_of_accounts');
            $table->decimal('amount', 12, 2);
            $table->string('payment_method')->nullable(); // cash, bank, card
            $table->text('description')->nullable();
            $table->string('customer_name')->nullable();
            $table->string('invoice_number')->nullable();
            $table->foreignId('created_by')->constrained('users');
            $table->timestamps();
        });

        // Expense Transactions
        Schema::create('expenses', function (Blueprint $table) {
            $table->id();
            $table->string('reference_number')->unique();
            $table->date('transaction_date');
            $table->foreignId('account_id')->constrained('chart_of_accounts');
            $table->decimal('amount', 12, 2);
            $table->string('payment_method')->nullable();
            $table->text('description')->nullable();
            $table->string('vendor_name')->nullable();
            $table->string('receipt_number')->nullable();
            $table->string('receipt_file')->nullable();
            $table->foreignId('created_by')->constrained('users');
            $table->timestamps();
        });

        // Journal Entries
        Schema::create('journal_entries', function (Blueprint $table) {
            $table->id();
            $table->string('entry_number')->unique();
            $table->date('entry_date');
            $table->text('description')->nullable();
            $table->foreignId('created_by')->constrained('users');
            $table->timestamps();
        });

        Schema::create('journal_entry_lines', function (Blueprint $table) {
            $table->id();
            $table->foreignId('journal_entry_id')->constrained()->onDelete('cascade');
            $table->foreignId('account_id')->constrained('chart_of_accounts');
            $table->decimal('debit', 12, 2)->default(0);
            $table->decimal('credit', 12, 2)->default(0);
            $table->text('description')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('journal_entry_lines');
        Schema::dropIfExists('journal_entries');
        Schema::dropIfExists('expenses');
        Schema::dropIfExists('incomes');
        Schema::dropIfExists('chart_of_accounts');
    }
};
