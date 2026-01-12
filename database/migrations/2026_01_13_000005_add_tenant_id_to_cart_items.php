<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('cart_items', function (Blueprint $table) {
            $table->string('tenant_id')->nullable()->after('id')->index();
        });

        // Backfill tenant_id from parent carts
        $driver = DB::getDriverName();
        if ($driver === 'sqlite') {
            DB::update("
                UPDATE cart_items
                SET tenant_id = (SELECT tenant_id FROM carts WHERE carts.id = cart_items.cart_id)
            ");
        } else {
             DB::update("
                UPDATE cart_items
                JOIN carts ON carts.id = cart_items.cart_id
                SET cart_items.tenant_id = carts.tenant_id
            ");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cart_items', function (Blueprint $table) {
             $table->dropColumn('tenant_id');
        });
    }
};
