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
        Schema::create('user_waivers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('waiver_id')->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->json('user_waiver_accepted_data')->nullable();
            $table->text('signature')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_waivers');
    }
};
