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
        Schema::create('creditpacks', function (Blueprint $table) {
            $table->id();
            $table->boolean('status')->default(false);
            $table->string('title', 255);
            $table->string('line1', 255)->nullable();
            $table->string('line2', 255)->nullable();
            $table->string('line3', 255)->nullable();
            $table->string('line4', 255)->nullable();
            $table->integer('credits');
            $table->decimal('price', 8, 2);
            $table->boolean('is_private')->default(false);
            $table->string('private_url', 512)->nullable();
            $table->date('active_from')->nullable()->comment('pack available for purchase from');
            $table->date('active_to')->nullable()->comment('pack available for purchase to');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('creditpacks');
    }
};
