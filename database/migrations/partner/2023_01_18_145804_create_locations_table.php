<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('locations', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title', 255);
            $table->string('page_title', 255);
            $table->string('brief', 500);
            $table->string('url', 255)->unique()->nullable();
            $table->string('checkin_url', 255)->unique()->nullable();
            $table->unsignedBigInteger('manager_id')->index('manager_id');
            $table->string('address_line_1', 255);
            $table->string('address_line_2', 255);
            $table->unsignedBigInteger('country_id')->index('country_id');
            $table->string('city', 255);
            $table->string('postcode', 50);
            $table->string('map_latitude', 50);
            $table->string('map_longitude', 50);
            $table->string('tel', 50);
            $table->string('email', 255);
            $table->integer('ordering')->default(0);
            $table->unsignedTinyInteger('status')->default(1);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('locations');
    }
};
