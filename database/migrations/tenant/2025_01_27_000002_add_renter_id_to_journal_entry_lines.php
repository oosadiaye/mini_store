<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('journal_entry_lines')) {
            Schema::table('journal_entry_lines', function (Blueprint $table) {
                if (!Schema::hasColumn('journal_entry_lines', 'renter_id')) {
                    $table->unsignedBigInteger('renter_id')->nullable()->after('account_id');
                    $table->index('renter_id');
                }
            });
        }
    }

    public function down(): void
    {
        Schema::table('journal_entry_lines', function (Blueprint $table) {
            $table->dropIndex(['renter_id']);
            $table->dropColumn('renter_id');
        });
    }
};
