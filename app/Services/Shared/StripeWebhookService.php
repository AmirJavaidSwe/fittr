<?php

namespace App\Services\Shared;

use App\Models\StripeEvent;
use Illuminate\Http\Response;

// Main job of this class is to dispatch actions received from Stripe webhook event
// Every method in this class supposed to handle specific event type.

class StripeWebhookService
{
    public function __construct()
    {

    }

    public function checkoutSessionCompleted(StripeEvent $event)
    {
        //this is a sample method to handle single specific event type
        //in any case all calls Stripe makes to our webhook must return status code 200 when we actually received it, otherwise new attempts will be made by Stripe

        // NOTE!
        // This event may indicate that payment succeeded (credit cards) but if customers authorized debit from any of the following payment methods: 
        // Bacs Direct Debit, Bank transfers, Boleto, Canadian pre-authorized debits, Konbini, OXXO, SEPA Direct Debit, SOFORT, or ACH Direct Debit
        // confirmation may come later with 'checkout.session.async_payment_succeeded'
        // In any case we must check if $session->payment_status == 'paid' before fulfilling order
        return new Response("Event {$event->stripe_id} received ok", 200);
    }

    public function checkoutSessionAsyncPaymentSucceeded(StripeEvent $event)
    {
        return new Response("Event {$event->stripe_id} received ok", 200);
    }

    public function checkoutSessionAsyncPaymentFailed(StripeEvent $event)
    {
        return new Response("Event {$event->stripe_id} received ok", 200);
    }

    public function paymentIntentSucceeded(StripeEvent $event)
    {
        return new Response("Event {$event->stripe_id} received ok", 200);
    }


}