<?php

use App\Enums\ClassStatuses;
use App\Models\ClassLesson;
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
            $table->string('name');
            $table->enum('status', ClassStatuses::GET_VALUES)->default(ClassStatuses::INACTIVE->value);
            $table->boolean('peak_of_peak_setting')->nullable();
            $table->timestamp('start_date')->nullable();
            $table->timestamp('end_date')->nullable();
            $table->integer('instructor_id')->unsigned();
            $table->integer('studio_id')->nullable()->unsigned();
            $table->boolean('does_repeat')->default(false);
            $table->json('week_days')->nullable();
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
