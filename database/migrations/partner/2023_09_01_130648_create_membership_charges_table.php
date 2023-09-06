<?php

use App\Enums\StripeInvoiceStatus;
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
        Schema::create('membership_charges', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('membership_id')->unsigned()->nullable();
            $table->bigInteger('user_id')->unsigned()->nullable();
            $table->string('stripe_subscription_id')->nullable(); //recurring only
            $table->boolean('is_paid')->default(false);
            $table->boolean('is_processed')->default(false); //connected to membership and payload processed (recurring subscription_cycle, subscription_update)
            $table->string('invoice_number')->nullable();
            $table->string('stripe_invoice_id')->nullable();
            $table->string('stripe_charge_id')->nullable();
            $table->string('stripe_payment_intent_id')->nullable(); //The PaymentIntent associated with this invoice, generated when the invoice is finalized, and can then be used to pay the invoice. 
            $table->enum('stripe_status', StripeInvoiceStatus::all()); //one of draft, open, paid, uncollectible, or void
            $table->string('currency', 3)->nullable(); // Three-letter ISO currency code, in lowercase. Must be a supported currency.
            $table->integer('amount_due')->unsigned()->default(0); //stripe integer
            $table->integer('amount_paid')->unsigned()->default(0);
            $table->integer('amount_remaining')->unsigned()->default(0);
            $table->integer('discount')->unsigned()->nullable();
            $table->integer('subtotal')->unsigned()->default(0);
            $table->integer('total')->unsigned()->default(0);
            $table->integer('application_fee_amount')->unsigned()->default(0); //collected by stripe and passed to parent (this fee is set on session chechout for connected account)
            $table->integer('period_start')->unsigned()->nullable(); //Start of the usage period during which invoice items were added to this invoice
            $table->integer('period_end')->unsigned()->nullable(); //End of the usage period during which invoice items were added to this invoice
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('membership_charges');
    }
};
