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
        Schema::table('support_tickets', function (Blueprint $table) {
            $table->string('contact_name')->nullable()->after('subject');
            $table->string('contact_email')->nullable()->after('contact_name');
        });

        Schema::table('ticket_messages', function (Blueprint $table) {
            // Make user_id nullable for guest messages
            $table->unsignedBigInteger('user_id')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('ticket_messages', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id')->nullable(false)->change();
        });

        Schema::table('support_tickets', function (Blueprint $table) {
            $table->dropColumn(['contact_name', 'contact_email']);
        });
    }
};
