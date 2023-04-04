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
        Schema::create('studios', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title', 250);
            $table->string('page_title', 250);
            $table->string('brief', 500);
            $table->string('address_line_1');
            $table->string('address_line_2');
            $table->string('city');
            $table->string('county');
            $table->string('postcode');
            $table->string('tel', 50);
            $table->string('map_latitude', 50);
            $table->string('map_longitude', 50);
            $table->integer('ordering');
            $table->timestamps();
            $table->softDeletes();
            $table->tinyInteger('status')->index();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('studios');
    }
};
