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
        Schema::create('stripe_events', function (Blueprint $table) {
            $table->id();
            $table->string('stripe_id', 255);
            $table->string('connected_account', 255)->nullable(); //connected account of partner
            $table->string('type', 255);
            $table->enum('event_for', ['connected', 'local']);
            $table->boolean('is_processed')->default(false);
            $table->string('object', 255)->comment('name'); //should be "event" all the time?
            $table->string('api_version', 255)->nullable();
            $table->json('data')->nullable();
            $table->boolean('livemode');
            $table->integer('created_at_ts')->nullable();
            $table->timestamps();
        });
    }
    

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('stripe_events');
    }
};
