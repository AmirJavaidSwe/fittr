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
            $table->unsignedBigInteger('studio_id')->index('studio_id');
            $table->unsignedBigInteger('class_type_id')->index('class_type_id');
            $table->unsignedSmallInteger('spaces');
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
