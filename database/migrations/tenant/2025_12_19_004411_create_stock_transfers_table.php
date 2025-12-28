<?php

use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up()
    {
        // Intentionally empty.
        // This table is already correctly created by 2025_01_01_100001_create_stock_transfers_table.php
        // This file previously caused a duplicate table error and missing tenant_id.
    }

    public function down()
    {
        // Intentionally empty
    }
};
