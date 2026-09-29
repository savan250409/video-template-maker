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
        Schema::create('schedule_notification', function (Blueprint $table) {
            $table->id();
            $table->integer('template_id');
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('image', 200)->nullable();
            $table->date('date');
            $table->time('time');
            $table->integer('is_active')->default(1);
            $table->integer('is_sent')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('schedule_notification');
    }
};
