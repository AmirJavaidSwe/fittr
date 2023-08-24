<?php

namespace App\Services\Shared;

use App\Enums\PackType;
use App\Enums\StateType;
use App\Enums\StripeCheckoutMode;
use App\Enums\StripePaymentStatus;
use App\Enums\StripeSessionStatus;
use App\Events\OrderPaid;
use App\Models\Partner\Membership;
use App\Models\Partner\Order;
use App\Models\Partner\OrderItem;
use App\Models\Partner\SessionCredit;
use App\Models\Partner\User;
use App\Models\StripeEvent;
use App\Services\Partner\DatabaseConnectionService;
use App\Services\Shared\Stripe\PaymentService;

class FulfillmentService
{
    public function __construct(DatabaseConnectionService $db_service, PaymentService $stripe_payment_service)
    {
        $this->db_service = $db_service;
        $this->stripe_payment_service = $stripe_payment_service;
    }

    public function processCheckout(StripeEvent $stripe_event)
    {
        if($stripe_event->is_processed){
            return;
        }
        // checkout could be for partner users (majority) or for partner as customer (business subscribes to service tier)
        // $stripe_event->event_for is an enum of 'connected', 'local'
        // $business has stripe_customer_id (business is a customer, for 'local' events) and stripe_account_id (foreign key to connected account, for 'connected' events)

        //beta flow for 'connected':
        $business = $stripe_event->business ?? null;
        if(empty($business)){
            return;
        }

        //set database connection
        $this->db_service->connect($business);

        // json data full event object
        $session = $stripe_event->data;

        //check for existing?
        $order = Order::where('trace', $session->client_reference_id)->with(['items'])->first();

        if($order && $order->line_items_pulled /*is_processed*/){
            //make sure that each of the items has been processed
            $items_processed = $order->items->every(fn($item) => $item->is_processed);

            return $items_processed ? $this->markEventProcessed($stripe_event) : null;

            //TODO flow to cover the case when some of order_items exist in DB but hasn't beed processed on initial run
        }

        // pull line items from Stripe checkout session
        // Retrieve checkout session
        $connected_account_id = $stripe_event->connected_account;
        $params = ['expand' => ['line_items.data.discounts']];
        $remote = $this->stripe_payment_service->retrieveCheckoutSession($connected_account_id, $session->id, $params);
        if($remote->error){
            //all stripe webhooks will get 200 back
            //if anything failed on our end, we have to take care of processing $stripe_event again
            //log error
            return;
        }
        
        $session_stripe = $remote->data;

         //create new order, if doesn't exist
        $order = $order ?: $this->createOrder($session_stripe, $stripe_event->id);

        // assign user to order
        $this->updateOrderUser($order);

        // create new OrderItem for each of line items (is_processed is false by default)
        if($order->line_items_pulled === false || $order->items->isEmpty()){
            $stripe_line_items = $session_stripe->line_items->data;
            $this->createOrderItems($order, $stripe_line_items);
        }

        //update order, set line_items_pulled = true; line_items count (this prevents related count), refresh the model and load related order_items
        $this->updateOrder($order);

        // assign price_id to each order_item
        $this->updateOrderItems($order);

        // Process each line item:
        //  create new Membership for type 'pack'
        //  create session credits (conditionally)
        //  mark each order_item 'is_processed'
        $order->items->each(fn($order_item) => $this->processOrderItem($order_item));

        // send confirmation email (queued event)
        event(new OrderPaid($order));

        //mark stripe event as processed
        return $this->markEventProcessed($stripe_event);
    }

    public function markEventProcessed($stripe_event) : bool
    {
        return $stripe_event->update(['is_processed' => true]);
    }

    public function markOrderItemProcessed($order_item) : bool
    {
        return $order_item->update(['is_processed' => true]);
    }

    public function createOrder($session, $stripe_event_id = null) : Order
    {
        return Order::create([
            'user_id' => null, //default
            'stripe_event_id' => $stripe_event_id,
            'stripe_customer_id' => $session->customer,
            'session_id' => $session->id,
            'checkout_mode' => StripeCheckoutMode::get($session->mode),
            'checkout_status' => StripeSessionStatus::get($session->status),
            'payment_status' => StripePaymentStatus::get($session->payment_status),
            'payment_intent' => $session->payment_intent,
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
                $this->createSessionCredits($membership);
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
                'order_id' => $order_item->order_id,
                'pack_id' => $pack->id,
                'pack_price_id' => $pack_price->id,
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

    public function createSessionCredits($membership) : void
    {
        // Memberships of type Class, Service and Hybrid are only types that can create new credits
        if(PackType::creditable($membership->type) === false){
            return;
        }
        // Membership must not be unlimited (is_unlimited == false), sessions > 0
        if($membership->is_unlimited || !($membership->sessions > 0) ){
            return;
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

        for ($i = 1; $i <= $membership->sessions; $i++) { 
            SessionCredit::create([
                'user_id' => $membership->user_id,
                'order_id' => $membership->order_id,
                'order_item_id' => $membership->order_item_id,
                'membership_id' => $membership->id,
                'status' => StateType::get('active'),
                'price_value' => round($membership->price / $membership->sessions, 2),
                'restrictions' => $membership->restrictions, //inherits from membership
                'expiry_at' => $expiry_at ?? null, // credit does not expire if null
            ]);
        }

    }
}