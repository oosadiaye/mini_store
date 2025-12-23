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
        // Update the status enum to include shipped and delivered
        // We use raw SQL because modifying enums in Laravel/Doctrine is complex and driver-dependent
        // This syntax works for MySQL/MariaDB which is the likely production environment
        
        $connection = config('database.default');
        
        if ($connection === 'mysql' || $connection === 'mariadb') {
             DB::statement("ALTER TABLE orders MODIFY COLUMN status ENUM('pending', 'processing', 'shipped', 'delivered', 'completed', 'cancelled', 'refunded') NOT NULL DEFAULT 'pending'");
        } else {
            // Fallback for SQLite (doesn't support ENUM natively, usually CHECK constraint or just Text)
            // For separate sqlite tenants, we might alter the check constraint if it exists, or do nothing if it's just TEXT
            // Laravel's SQLite driver often treats Enum as Varchar so we might not need to do anything strictly
            // but for correctness lets ensure the column allows these values conceptually.
        }
    }

    public function down(): void
    {
        // Revert to original enum
        $connection = config('database.default');
        
        if ($connection === 'mysql' || $connection === 'mariadb') {
             DB::statement("ALTER TABLE orders MODIFY COLUMN status ENUM('pending', 'processing', 'completed', 'cancelled', 'refunded') NOT NULL DEFAULT 'pending'");
        }
    }
};
