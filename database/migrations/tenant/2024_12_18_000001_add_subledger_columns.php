<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Chart of Accounts - Mark accounts as Control Accounts
        Schema::table('chart_of_accounts', function (Blueprint $table) {
            $table->string('sub_ledger_type')->nullable()->after('account_type'); // 'customer', 'supplier', null
        });

        // 2. Journal Entry Lines - Link to Entity
        Schema::table('journal_entry_lines', function (Blueprint $table) {
            $table->nullableMorphs('entity'); // entity_type, entity_id
        });

        // 3. Incomes - Link to Customer Model
        Schema::table('incomes', function (Blueprint $table) {
            $table->foreignId('customer_id')->nullable()->constrained('customers')->after('account_id');
        });

        // 4. Expenses - Link to Supplier Model
        Schema::table('expenses', function (Blueprint $table) {
            $table->foreignId('supplier_id')->nullable()->constrained('suppliers')->after('account_id');
        });
    }

    public function down(): void
    {
        Schema::table('expenses', function (Blueprint $table) {
            $table->dropForeign(['supplier_id']);
            $table->dropColumn('supplier_id');
        });
        Schema::table('incomes', function (Blueprint $table) {
            $table->dropForeign(['customer_id']);
            $table->dropColumn('customer_id');
        });
        Schema::table('journal_entry_lines', function (Blueprint $table) {
            $table->dropMorphs('entity');
        });
        Schema::table('chart_of_accounts', function (Blueprint $table) {
            $table->dropColumn('sub_ledger_type');
        });
    }
};
