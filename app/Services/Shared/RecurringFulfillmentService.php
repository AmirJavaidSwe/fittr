<?php

namespace App\Services\Shared;

use App\Models\Partner\Membership;
use App\Models\StripeEvent;

class RecurringFulfillmentService extends FulfillmentService
{
    public function processInvoicePaid(StripeEvent $stripe_event)
    {
        if($this->shouldAbort($stripe_event)){
            return;
        }
        $business = $stripe_event->business;

        //set database connection
        $this->db_service->connect($business);

        $invoice_object = $stripe_event->data;

        // NOTE: New subscriptions will fire 2 events: 'checkout.session.completed' and 'invoice.paid', likely in wrong order  (invoice billing_reason = subscription_create)
        // Existing subscriptions (invoice billing_reason = subscription_cycle,subscription_update) trigger single 'invoice.paid' event
        // $membership may not exist in database for new subscriptions, because 'invoice.paid' event may come before 'checkout.session.completed'
        $membership = Membership::where('stripe_subscription_id', $invoice_object->subscription)
        ->withTrashed()
        ->first();

        $membership_charge = $this->upsertMembershipCharge($invoice_object, $membership);

        if(!$membership_charge->is_processed && !empty($membership)){
            //process payload and mark charge as processed
            $this->createSessionCredits($membership);
            $this->markMembershipChargeProcessed($membership_charge);
        }

        //mark stripe event as processed
        return $this->markEventProcessed($stripe_event);
    }
}