<?php

namespace App\Services\Shared\Stripe;

use App\Models\StripeEvent;
use Illuminate\Http\Response;
use App\Services\Shared\FulfillmentService;

// Main job of this class is to dispatch actions received from Stripe webhook event
// Every method in this class supposed to handle specific event type.

class WebhookService
{
    public function __construct(FulfillmentService $fulfillment_service)
    {
        $this->fulfillment_service = $fulfillment_service;
    }

    public function response($event, $for = 'connected')
    {
        return $for == 'connected' ? 
            new Response("Event {$event->stripe_id} for connected account received ok", 200):
            new Response("Local event {$event->stripe_id} received ok", 200);
    }

    public function processCheckout($event) : void
    {
        try {
            $this->fulfillment_service->processCheckout($event);
        } catch (\Exception $e) {
            //TODO log/alert
        }
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
        if($event->event_for == 'local'){
            //do nothing for now (partner tier subscription)
            return $this->response($event, $event->event_for);
        }
        $this->processCheckout($event);

        return $this->response($event, $event->event_for);
    }

    public function checkoutSessionAsyncPaymentSucceeded(StripeEvent $event)
    {
        if($event->event_for == 'local'){
            //do nothing for now (partner tier subscription)
            return $this->response($event, $event->event_for);
        }
        $this->processCheckout($event);

        return $this->response($event, $event->event_for);
    }

    public function checkoutSessionAsyncPaymentFailed(StripeEvent $event)
    {
        if($event->event_for == 'local'){
            //do nothing for now (partner tier subscription)
            return $this->response($event, $event->event_for);
        }

        return $this->response($event, $event->event_for);
    }

    // public function paymentIntentSucceeded(StripeEvent $event)
    // {
    //     return $this->response($event, $event->event_for);
    // }

}