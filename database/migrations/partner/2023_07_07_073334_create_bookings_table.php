<?php

use App\Enums\BookingStatus;
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
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->enum('status', BookingStatus::all())->default(BookingStatus::get('active'));
            $table->unsignedBigInteger('class_id')->index('class_id');
            $table->unsignedBigInteger('user_id')->index('user_id');
            $table->boolean('is_family_booking')->nullable()->comment('true if group');
            $table->unsignedBigInteger('family_member_id')->nullable();
            $table->integer('space_number')->unsigned()->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
