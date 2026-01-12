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
        // Update product_images with tenant_id from parent product
        $driver = DB::getDriverName();

        if ($driver === 'sqlite') {
            DB::update("
                UPDATE product_images
                SET tenant_id = (SELECT tenant_id FROM products WHERE products.id = product_images.product_id)
                WHERE tenant_id IS NULL
            ");
        } elseif ($driver === 'mysql') {
            DB::update("
                UPDATE product_images
                JOIN products ON products.id = product_images.product_id
                SET product_images.tenant_id = products.tenant_id
                WHERE product_images.tenant_id IS NULL
            ");
        } else {
             // Fallback for PostgreSQL etc
            DB::update("
                UPDATE product_images
                SET tenant_id = (SELECT tenant_id FROM products WHERE products.id = product_images.product_id)
                WHERE tenant_id IS NULL
            ");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No need to reverse really, but strictly speaking we could nullify them
    }
};
