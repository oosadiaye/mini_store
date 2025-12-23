<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('theme_settings', function (Blueprint $table) {
            $table->json('colors')->nullable();
            $table->json('fonts')->nullable();
            $table->json('layout_settings')->nullable();
            $table->text('custom_css')->nullable();
            $table->boolean('is_active')->default(false);
            $table->unsignedBigInteger('template_id')->nullable();
            // Foreign key removed - storefront_templates table doesn't exist yet
            // Can be added later when the table is created
        });
    }

    public function down()
    {
        Schema::table('theme_settings', function (Blueprint $table) {
            // No foreign key to drop since it was removed
            $table->dropColumn(['colors', 'fonts', 'layout_settings', 'custom_css', 'is_active', 'template_id']);
        });
    }
};
