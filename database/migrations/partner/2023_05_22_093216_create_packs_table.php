<?php

use App\Enums\PackType;
use App\Enums\StripePeriod;
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
        Schema::create('packs', function (Blueprint $table) {
            $table->id();
            $table->enum('type', PackType::all())->default(PackType::default->name)->comment('enum');
            $table->string('title', 255);
            $table->string('sub_title', 255)->nullable();
            $table->text('description')->nullable();
            $table->string('stripe_product_id')->nullable();
            $table->boolean('is_active')->default(false)->comment('bool'); //global state
            $table->boolean('is_restricted')->default(false)->comment('bool'); //Indicator for book session limitations: off-peak, class types
            $table->boolean('is_private')->default(false)->comment('bool');
            $table->json('restrictions')->nullable()->comment('json'); //Object having applicable restrictions by class type
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
        Schema::dropIfExists('packs');
    }
};
