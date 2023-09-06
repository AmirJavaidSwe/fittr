<?php

use App\Enums\StripeCheckoutMode;
use App\Enums\StripePaymentStatus;
use App\Enums\StripeSessionStatus;
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
            $table->bigInteger('stripe_event_id')->unsigned()->nullable();
            $table->string('stripe_customer_id', 255)->nullable();
            $table->enum('checkout_mode', StripeCheckoutMode::all());
            $table->enum('checkout_status', StripeSessionStatus::all());
            $table->enum('payment_status', StripePaymentStatus::all());
            $table->string('session_id', 255)->nullable()->comment('sc_'); //Stripe sc_...
            $table->string('subscription_id', 255)->nullable()->comment('sub_'); // Stripe sub_... recurring only (checkout_mode = subscription)
            $table->string('invoice_id', 255)->nullable()->comment('in_'); // Stripe in_... recurring only (checkout_mode = subscription)
            $table->string('payment_intent', 255)->nullable()->comment('pi_'); // Stripe pi_...
            $table->uuid('trace')->nullable();
            $table->string('currency', 3)->nullable(); //ISO 3 chars
            $table->integer('amount_discount')->unsigned()->default(0); //stripe integer
            $table->integer('amount_subtotal')->unsigned()->default(0); //stripe integer
            $table->integer('amount_total')->unsigned()->default(0); //stripe integer
            $table->boolean('line_items_pulled')->default(false)->comment('checkout session');
            $table->integer('line_items')->unsigned()->default(0); //total number of checkout session line items (order_items)
            // $table->boolean('is_processed')->default(false)->comment('whole'); // main flag for order completion
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
