<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    protected $tables = [
        'announcements',
        'announcement_reads',
        'audit_logs',
        'carts',
        'contact_messages',
        'journal_entry_lines',
        'pages',
        'page_sections',
        'posts',
        'reviews',
        'store_collections',
        'ticket_categories',
        'ticket_messages',
        'product_warehouse',
    ];

    public function up(): void
    {
        foreach ($this->tables as $tableName) {
            if (Schema::hasTable($tableName) && !Schema::hasColumn($tableName, 'tenant_id')) {
                Schema::table($tableName, function (Blueprint $table) use ($tableName) {
                    if (Schema::hasColumn($tableName, 'id')) {
                        $table->string('tenant_id')->nullable()->after('id')->index();
                    } else {
                        $table->string('tenant_id')->nullable()->index();
                    }
                });
            }
        }
    }

    public function down(): void
    {
        foreach ($this->tables as $tableName) {
            if (Schema::hasTable($tableName) && Schema::hasColumn($tableName, 'tenant_id')) {
                Schema::table($tableName, function (Blueprint $table) {
                    $table->dropIndex(['tenant_id']);
                    $table->dropColumn('tenant_id');
                });
            }
        }
    }
};
