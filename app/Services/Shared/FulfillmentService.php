<?php

namespace App\Services\Shared;

use App\Enums\PackType;
use App\Enums\StateType;
use App\Enums\StripeCheckoutMode;
use App\Enums\StripeInvoiceStatus;
use App\Enums\StripePaymentStatus;
use App\Enums\StripePriceType;
use App\Enums\StripeSessionStatus;
use App\Models\Partner\Membership;
use App\Models\Partner\MembershipCharge;
use App\Models\Partner\Order;
use App\Models\Partner\OrderItem;
use App\Models\Partner\SessionCredit;
use App\Models\Partner\User;
use App\Models\StripeEvent;
use App\Services\Partner\DatabaseConnectionService;
use App\Services\Shared\Stripe\InvoiceService;
use App\Services\Shared\Stripe\PaymentService;

class FulfillmentService
{
    public function __construct(DatabaseConnectionService $db_service, InvoiceService $stripe_invoice_service, PaymentService $stripe_payment_service)
    {
        $this->db_service = $db_service;
        $this->stripe_invoice_service = $stripe_invoice_service;
        $this->stripe_payment_service = $stripe_payment_service;
    }

    public function shouldAbort(StripeEvent $stripe_event): bool
    {
        if($stripe_event->is_processed){
            return true;
        }

        $business = $stripe_event->business ?? null;
        if(empty($business)){
            return true;
        }
        return false;
    }

    public function markEventProcessed($stripe_event) : bool
    {
        return $stripe_event->update(['is_processed' => true]);
    }

    public function markOrderItemProcessed($order_item) : bool
    {
        return $order_item->update(['is_processed' => true]);
    }

    public function markMembershipChargeProcessed($membership_charge) : bool
    {
        return $membership_charge->update(['is_processed' => true]);
    }

    public function createOrder($session, $stripe_event_id = null) : Order
    {
        return Order::create([
            'user_id' => null, //default
            'stripe_event_id' => $stripe_event_id,
            'stripe_customer_id' => $session->customer,
            'checkout_mode' => StripeCheckoutMode::get($session->mode),
            'checkout_status' => StripeSessionStatus::get($session->status),
            'payment_status' => StripePaymentStatus::get($session->payment_status),
            'session_id' => $session->id, //Stripe ID, not a local foreign key
            'subscription_id' => $session->subscription, //Stripe ID, not a local foreign key
            'invoice_id' => $session->invoice, //Stripe ID, not a local foreign key
            'payment_intent' => $session->payment_intent, //Stripe ID, not a local foreign key
            'trace' => $session->client_reference_id,
            'currency' => $session->currency,
            'amount_discount' => $session->total_details->amount_discount,
            'amount_subtotal' => $session->amount_subtotal,
            'amount_total' => $session->amount_total,
            'line_items_pulled' => false, //default
            'line_items' => 0, //default
        ]);
    }

    public function updateOrder($order) : void
    {
        $order->load('items');
        $order->update(['line_items_pulled' => true, 'line_items' => $order->items->count()]);
    }

    public function updateOrderUser($order) : void
    {
        if(empty($order->user_id)){
            $user = User::where('stripe_id', $order->stripe_customer_id)->first();
            $order->update(['user_id' => $user?->id]);
        }
        $order->load('user');
    }

    public function createOrderItems($order, $stripe_line_items) : \Illuminate\Support\Collection
    {
        $order_items = collect();

        if(empty($stripe_line_items)) {
            return $order_items;
        }
        foreach ($stripe_line_items as $stripe_line_item) {
            $order_item = OrderItem::create([
                'order_id' => $order->id,
                'user_id' => $order->user_id,
                'pack_price_id' => null,
                'stripe_price_id' => $stripe_line_item->price->id,
                'quantity' => $stripe_line_item->quantity,
                'currency' => $order->currency,
                'unit_amount' => $stripe_line_item->price->unit_amount,
                'amount_discount' => $stripe_line_item->amount_discount,
                'amount_subtotal' => $stripe_line_item->amount_subtotal,
                'amount_total' => $stripe_line_item->amount_total,
            ]);
            $order_items->push($order_item);
        }
        return $order_items;
    }

    public function updateOrderItems($order) : void
    {
        $order_items = $order->items->load(['price_stripe']);
        $order_items->each(function ($order_item) {
            $order_item->update(['pack_price_id' => $order_item->price_stripe?->id]);
        });
        $order->items->load(['pack_price.priceable', 'pack_price.locations']);
    }

    public function processOrderItem($order_item) : void
    {
        if($order_item->is_processed){
            return;
        }

        $pack_price = $order_item->pack_price;
        if(empty($pack_price)){
            // order_item has no stripe price related model - possible with add-on/upsale line items
            // note that we must be able to process pack_price and pack, both relationships pulled with trashed() method.
            // This condition can occur when partner deletes pack or pack_price after checkout started by user.
            return;
        }

        switch ($pack_price->priceable_type) {
            case 'pack':
                $membership = $this->createMembership($order_item);
                $session_credits = $this->createSessionCredits($membership);

                if($membership->billing_type == StripePriceType::get('recurring')){
                    //MembershipCharge could already exist in database for new subscription, happens when 'invoice.paid' received before 'checkout.session.completed'
                    //in such case $membership_charge has no `membership_id` and `user_id`
                    $stripe_invoice_id = $order_item->order->invoice_id;
                    $existing_charge = MembershipCharge::where('stripe_invoice_id', $stripe_invoice_id)->first();
                    if($existing_charge){
                        $existing_charge->update(['membership_id' => $membership->id, 'user_id' => $membership->user_id, 'is_processed' => true]);
                    }
                    //if no $existing_charge, 'invoice.paid' webhook event will take care of $membership_charge creation with upsertMembershipCharge($invoice_object, $membership)
                }

                break;
            
            default:
                //future types go above, unhandeled no action
                break;
        }

        $this->markOrderItemProcessed($order_item);
    }

    public function createMembership($order_item) : Membership
    {
        $pack_price = $order_item->pack_price;
        $order = $order_item->order;
        $pack = $pack_price->priceable;

        $restrictions = $pack->is_restricted ? array_filter($pack->restrictions) : []; // keep non empty only
        //pack restrictions is a json array, membership and credits inherit all restrictions from pack + location restrictions from pack_price
        $location_restrictions = $pack_price->locations->pluck('id')->toArray();
        if(!empty($location_restrictions)){
            $restrictions['locations'] = $location_restrictions;
        }
        if(empty($restrictions)){
            $restrictions = null;
        }

        return Membership::firstOrCreate(
            [
                'user_id' => $order_item->user_id,
                'order_item_id' => $order_item->id,
            ],
            [
                'status' => StateType::get('active'),
                'order_id' => $order_item->order_id,
                'pack_id' => $pack->id,
                'pack_price_id' => $pack_price->id,
                'stripe_subscription_id' => $order->subscription_id,
                'type' => $pack->type,
                'title' => $pack->title,
                'sub_title' => $pack->sub_title,
                'description' => $pack->description,
                'is_restricted' => $pack->is_restricted,
                'restrictions' => $restrictions,
                'billing_type' => $pack_price->type,
                'sessions' => $pack_price->sessions,
                'is_expiring' => $pack_price->is_expiring,
                'expiration' => $pack_price->expiration,
                'expiration_period' => $pack_price->expiration_period,
                'price' => $pack_price->price,
                'currency' => $pack_price->currency,
                'currency_symbol' => $pack_price->currency_symbol,
                'interval' => $pack_price->interval,
                'interval_count' => $pack_price->interval_count,
                'is_unlimited' => $pack_price->is_unlimited,
                'is_fap' => $pack_price->is_fap,
                'fap_value' => $pack_price->fap_value,
                'is_ongoing' => $pack_price->is_ongoing,
                'fixed_count' => $pack_price->fixed_count,
                'is_renewable' => $pack_price->is_renewable,
                'is_intro' => $pack_price->is_intro,
            ]
        );
    }

    public function upsertMembershipCharge($invoice_object, $membership = null) : MembershipCharge
    {
        return MembershipCharge::updateOrCreate(
            ['stripe_invoice_id' => $invoice_object->id],
            [
                'membership_id' => $membership->id ?? null,
                'user_id' => $membership->user_id ?? null,
                'stripe_subscription_id' => $invoice_object->subscription,
                'is_paid' => $invoice_object->paid,
                'invoice_number' => $invoice_object->number,
                'stripe_charge_id' => $invoice_object->charge,
                'stripe_payment_intent_id' => $invoice_object->payment_intent,
                'stripe_status' => StripeInvoiceStatus::get($invoice_object->status),
                'currency' => $invoice_object->currency,
                'amount_due' => $invoice_object->amount_due,
                'amount_paid' => $invoice_object->amount_paid,
                'amount_remaining' => $invoice_object->amount_remaining,
                'discount' => $invoice_object->discount,
                'subtotal' => $invoice_object->subtotal,
                'total' => $invoice_object->total,
                'application_fee_amount' => $invoice_object->application_fee_amount,
                'period_start' => $invoice_object->period_start,
                'period_end' => $invoice_object->period_end,
            ]
        );
    }

    public function createSessionCredits($membership) : ?\Illuminate\Support\Collection
    {
        // Memberships of type Class, Service and Hybrid can create new credits
        // Memberships of type location_pass, may create credits if sessions > 0 (pass_mode)
        if(PackType::creditable($membership->type) === false){
            return null;
        }

        // Membership must not be unlimited (is_unlimited == false), sessions > 0
        if($membership->is_unlimited || !($membership->sessions > 0) ){
            return null;
        }

        $pack_price = $membership->pack_price;
        if($pack_price->is_expiring){
            $expiry_at = today();
            switch ($pack_price->expiration_period) {
                case 'day':
                    $expiry_at->addDays($pack_price->expiration);
                    break;
                case 'week':
                    $expiry_at->addWeeks($pack_price->expiration);
                    break;
                case 'month':
                    $expiry_at->addMonths($pack_price->expiration);
                    break;
                case 'year':
                    $expiry_at->addYears($pack_price->expiration);
                    break;
            }
        }

        $session_credits = collect();
        for ($i = 1; $i <= $membership->sessions; $i++) { 
            $session_credit = SessionCredit::create([
                'user_id' => $membership->user_id,
                'order_id' => $membership->order_id,
                'order_item_id' => $membership->order_item_id,
                'membership_id' => $membership->id,
                'status' => StateType::get('active'),
                'type' => $membership->type,
                'price_value' => round($membership->price / $membership->sessions, 2),
                'restrictions' => $membership->restrictions, //inherits from membership
                'expiry_at' => $expiry_at ?? null, // credit does not expire if null
            ]);
            $session_credits->push($session_credit);
        }
        return $session_credits;
    }
}