<?php

use App\Enums\ClassStatus;
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
        Schema::create('classes', function (Blueprint $table) {
            $table->id();
            $table->string('title', 255);
            $table->enum('status', ClassStatus::all())->default(ClassStatus::INACTIVE->value);
            $table->boolean('is_off_peak')->default(false);
            $table->timestamp('start_date')->nullable();
            $table->timestamp('end_date')->nullable();
            $table->integer('instructor_id')->unsigned();
            $table->integer('class_type_id')->unsigned();
            $table->integer('studio_id')->unsigned();
            $table->string('file_path')->nullable();
            $table->smallInteger('spaces')->unsigned()->nullable();
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
        Schema::dropIfExists('classes');
    }
};
