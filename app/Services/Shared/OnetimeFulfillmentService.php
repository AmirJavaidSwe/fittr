<?php

namespace App\Services\Shared;

use App\Events\OrderPaid;
use App\Models\Partner\Order;
use App\Models\StripeEvent;

class OnetimeFulfillmentService extends FulfillmentService
{
    public function processCheckout(StripeEvent $stripe_event)
    {
        if($this->shouldAbort($stripe_event)){
            return;
        }

        $business = $stripe_event->business;

        //set database connection
        $this->db_service->connect($business);

        // json data full event object (checkout.session)
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

}