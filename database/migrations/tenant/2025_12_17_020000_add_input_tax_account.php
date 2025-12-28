<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Account;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // This logic is already handled in ChartOfAccountsSeeder.
        // Removed to prevent "Field 'tenant_id' doesn't have a default value" error during migration.
    }

    public function down(): void
    {
    }
};
