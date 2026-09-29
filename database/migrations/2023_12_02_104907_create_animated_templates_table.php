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
        Schema::create('animated_templates', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id')->nullable();
            $table->integer('category_id')->nullable();
            $table->string('type', 10)->default('video');
            $table->string('title');
            $table->integer('total_image_count')->nullable();
            $table->integer('total_editable')->nullable();
            $table->string('zip')->nullable();
            $table->string('zip_original_name', 500)->nullable();
            $table->string('thumbnail')->nullable();
            $table->string('tags')->nullable();
            $table->integer('is_paid')->default(0);
            $table->integer('is_active')->default(1);
            $table->integer('total_views')->nullable();
            $table->integer('total_create')->nullable();
            $table->string('zip_folder')->nullable();
            $table->longText('json')->nullable();
            $table->string('height', 20)->nullable();
            $table->string('width', 20)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('animated_templates');
    }
};
