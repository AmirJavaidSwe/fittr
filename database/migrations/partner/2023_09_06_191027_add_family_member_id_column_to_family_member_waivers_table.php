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
        Schema::table('family_member_waivers', function (Blueprint $table) {
            $table->unsignedBigInteger('family_member_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('family_member_waivers', function (Blueprint $table) {
            $table->dropColumn('family_member_id');
            //
        });
    }
};
