<?php

use App\Enums\StripeCheckoutMode;
use App\Enums\StripePaymentStatus;
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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('user_id')->unsigned()->nullable();
            $table->enum('mode', StripeCheckoutMode::all());
            $table->enum('payment_status', StripePaymentStatus::all());
            $table->string('session_id', 255)->nullable();
            $table->string('payment_intent', 255)->nullable();
            $table->string('customer', 255)->nullable();
            $table->uuid('trace')->nullable();
            $table->integer('amount_subtotal')->unsigned()->default(0);
            $table->integer('amount_total')->unsigned()->default(0);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
