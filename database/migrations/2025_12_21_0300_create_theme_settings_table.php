<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        // Ensure a clean state
        Schema::dropIfExists('theme_settings');
        Schema::create('theme_settings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tenant_id');
            $table->string('theme_slug');
            $table->json('settings')->nullable();
            $table->timestamps();
            // Foreign key removed for simplicity
            $table->unique(['tenant_id', 'theme_slug']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('theme_settings');
    }
};






