<?php

use App\Enums\ClasspackType;
use App\Enums\ClasspackExpirationPeriod;
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
        Schema::create('class_packs', function (Blueprint $table) {
            $table->id();
            $table->boolean('is_active')->default(false)->comment('bool'); //global state
            $table->string('title', 255);
            $table->decimal('price', 8, 2)->unsigned()->default(0);
            $table->enum('type', ClasspackType::all())->default(ClasspackType::class_lesson->name)->comment('enum');
            $table->boolean('is_restricted')->default(false)->comment('bool'); //Indicator for book session limitations: off-peak, class types
            $table->json('restrictions')->nullable()->comment('class types');
            $table->integer('sessions')->unsigned()->comment('number'); //of bookings/sessions pack provides
            $table->boolean('is_expiring')->default(false)->comment('bool'); //Unused sessions will never expire
            $table->integer('expiration')->unsigned()->nullable()->comment('int'); //of X days, weeks, months, years. Unused sessions will expire after computed value.
            $table->enum('expiration_period', ClasspackExpirationPeriod::all())->nullable()->comment('enum, multiply expiration');
            $table->boolean('is_renewable')->default(false)->comment('bool'); //Auto purchase with saved card when sessions ran out or expired
            $table->boolean('is_intro')->default(false)->comment('bool'); //Available to members never made a purchase before. If true, is_renewable should be set false
            $table->boolean('is_private')->default(false)->comment('bool');
            $table->string('private_url', 512)->nullable(); //must be set if is_private
            $table->date('active_from')->nullable()->comment('available'); // for purchase from (not publicly listed/available for purchase until this date)
            $table->date('active_to')->nullable()->comment('available'); // for purchase to (not publicly listed/available for purchase after this date)
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('classpacks');
    }
};
