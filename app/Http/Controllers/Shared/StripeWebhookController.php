<?php

namespace App\Http\Controllers\Shared;

use App\Http\Controllers\Controller;
use App\Models\StripeEvent;
use App\Services\Shared\StripeWebhookService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Str;
use Stripe\Exception\SignatureVerificationException;
use Stripe\StripeClient;
use Stripe\Webhook;

class StripeWebhookController extends Controller
{
    public function __construct(StripeWebhookService $service)
    {
        $this->stripe = new StripeClient(config('services.stripe.secret_key'));
        $this->service = $service;
    }

    public function webhook(Request $request)
    {
        $payload = @file_get_contents('php://input');
        $sig_header = $_SERVER['HTTP_STRIPE_SIGNATURE'];
        $event = null;

        try {
            $event = Webhook::constructEvent(
                $payload,
                $sig_header,
                config('services.stripe.endpoint_secret')
            );
        } catch(SignatureVerificationException $e) {
            return new Response($e->getMessage(), 400);
        }

        //temp local test, delete this from production and uncomment above
        // $event = \Stripe\Event::constructFrom(
        //     json_decode($payload, true)
        // );

        // Handle the event
        return $this->handleEvent($event);
    }

    public function handleEvent($event)
    {
        // Handle duplicate events https://stripe.com/docs/webhooks/best-practices#duplicate-events
        // Webhook endpoints might occasionally receive the same event more than once. 
        // We advise you to guard against duplicated event receipts by making your event processing idempotent. 
        // One way of doing this is logging the events you’ve processed, and then not processing already-logged events
        $event_data = json_decode($event->toJSON());
        $existing = StripeEvent::where('stripe_id', $event_data->id)->first();

        if($existing){
            return new Response("Event {$event_data->id} has been received before", 200);
        }
        // store/log the event
        $stripe_event = StripeEvent::create([
            'stripe_id' => $event_data->id,
            'type' => $event_data->type,
            'object' => $event_data->object,
            'data' => $event_data->data->object,
            'api_version' => $event_data->api_version,
            'livemode' => $event_data->livemode,
            'created_at_ts' => $event_data->created,
        ]);

        //we may emit local event and listen for it but it's better to handle it immediatelly
        return $this->processEvent($stripe_event);
    }

    public function processEvent(StripeEvent $stripe_event)
    {
        $event_type = $stripe_event->type; // original type name, e.g.: 'checkout.session.completed' or 'payment_intent.succeeded'
        $method_name = Str::camel(str_replace('.', '_', $event_type)); //Convert type into camel case, string helper works great converting snake to camel

        return method_exists($this->service, $method_name) ? 
            $this->service->{ $method_name }($stripe_event) :
            new Response("Unsupported type: {$event_type}", 200);
    }
}
