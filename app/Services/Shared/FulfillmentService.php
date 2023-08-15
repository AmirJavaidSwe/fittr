<?php

namespace App\Services\Shared;

use App\Enums\StripeCheckoutMode;
use App\Enums\StripePaymentStatus;
use App\Models\Partner\Order;
use App\Models\StripeEvent;
use App\Services\Partner\DatabaseConnectionService;

class FulfillmentService
{
    public function __construct(DatabaseConnectionService $db_service)
    {
        $this->db_service = $db_service;
    }

    public function processCheckout(StripeEvent $stripe_event)
    {
        if($stripe_event->is_processed){
            return;
        }
        $business = $stripe_event->business ?? null;
        if(empty($business)){
            return;
        }

        //set database connection
        $this->db_service->connect($business);

        $session = $stripe_event->data; //full event object

        //check for existing?
        $order = Order::where('trace', $session->client_reference_id)->first();
        if($order){
            return;
        }

        //create new order
        $order = Order::create([
            'session_id' => $session->id,
            'payment_intent' => $session->payment_intent,
            'mode' => StripeCheckoutMode::get($session->mode),
            'payment_status' => StripePaymentStatus::get($session->payment_status),
            'customer' => $session->customer,
            'trace' => $session->client_reference_id,
            'amount_subtotal' => $session->amount_subtotal,
            'amount_total' => $session->amount_total,
        ]);

        //TODO:

        // pull line items from Stripe checkout session

        // create new OrderItem for each of line items

        // create new Membership

        // create session credits (conditionally)

        // send confirmation email (queued event)

        $stripe_event->update(['is_processed' => true]);

    }

}