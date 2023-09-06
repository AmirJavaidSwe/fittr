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
        Schema::rename('user_waivers', 'family_member_waivers');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::rename('family_member_waivers', 'user_waivers');
        //
    }

};