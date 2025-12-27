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
        Schema::table('subscription_transactions', function (Blueprint $table) {
            $table->string('old_plan_id')->nullable()->after('plan_id');
            $table->decimal('prorated_credit', 12, 2)->default(0)->after('amount');
            $table->integer('unused_days')->default(0)->after('prorated_credit');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('subscription_transactions', function (Blueprint $table) {
            $table->dropColumn(['old_plan_id', 'prorated_credit', 'unused_days']);
        });
    }
};
