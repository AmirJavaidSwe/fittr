<?php

namespace App\Services\Shared;

use App\Models\StripeEvent;
use Illuminate\Http\Response;

// Main job of this class is to dispatch actions received from Stripe webhook event
// Every method in this class suppose to handle specific event type.

class StripeWebhookService
{
    public function __construct()
    {

    }

    public function checkoutSessionCompleted(StripeEvent $event)
    {
        //this is a sample method to handle single specific event type
        //in any case all calls Stripe makes to our webhook must return status code 200 when we actually received it, otherwise new attempts will be made by Stripe
        return new Response("Event {$event->stripe_id} received ok", 200);
    }

}