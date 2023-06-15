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
        Schema::create('class_type_studios', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('location_id')->index('location_id');
            $table->unsignedBigInteger('class_type')->index('class_type');
            $table->integer('no_of_places');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('class_type_studios');
    }
};
