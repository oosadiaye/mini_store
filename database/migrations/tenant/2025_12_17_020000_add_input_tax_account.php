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
        if (Account::where('account_code', '1300')->doesntExist()) {
            Account::create([
                'account_code' => '1300',
                'account_name' => 'Input Tax Receivable',
                'account_type' => 'asset',
                'description' => 'Tax paid on purchases to be claimed',
                'is_active' => true,
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Account::where('account_code', '1300')->delete();
    }
};
