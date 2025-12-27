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
        if (!Schema::hasTable('plans')) {
            Schema::create('plans', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->decimal('price', 10, 2)->default(0);
                $table->integer('duration_days')->default(30);
                $table->json('features')->nullable(); // ["pos", "inventory", "domains"]
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }

        if (!Schema::hasColumn('tenants', 'subscription_ends_at')) {
            Schema::table('tenants', function (Blueprint $table) {
                $table->foreignId('plan_id')->nullable()->constrained('plans')->nullOnDelete();
                $table->timestamp('subscription_ends_at')->nullable();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tenants', function (Blueprint $table) {
            $table->dropForeign(['plan_id']);
            $table->dropColumn(['plan_id', 'subscription_ends_at']);
        });

        Schema::dropIfExists('plans');
    }
};
