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
        Schema::table('bookings', function (Blueprint $table) {
            $table->boolean('is_family_booking')->nullable()->after('user_id')->comment('For Family Members Booking');
            $table->unsignedBigInteger('family_member_id')->nullable()->after('is_family_booking')->comment('For Family Members Booking');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            //
            $table->dropColumn('is_family_booking');
            $table->dropColumn('family_member_id');
        });
    }
};
