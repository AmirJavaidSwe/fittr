<?php

namespace App\Http\Controllers\Shared;

use App\Http\Controllers\Controller;
use App\Models\StripeEvent;
use App\Services\Shared\Stripe\WebhookService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Str;
use Stripe\Exception\SignatureVerificationException;
use Stripe\StripeClient;
use Stripe\Webhook;

class StripeWebhookController extends Controller
{
    private $event_for;

    public function __construct(WebhookService $service)
    {
        $this->stripe = new StripeClient(config('services.stripe.secret_key'));
        $this->service = $service;
    }

    public function webhook(Request $request)
    {
        $payload = @file_get_contents('php://input');
        $sig_header = $_SERVER['HTTP_STRIPE_SIGNATURE'] ?? null;
        $event = null;
        //connect query is coming from Stripe webhook url setup
        //2 incoming webhooks hit same local route but have different secrets:
        // https://app.fittr.tech/stripe/webhook            Local is for partner admins actions like subscribing to Standard or Ultimate tier
        // https://app.fittr.tech/stripe/webhook?connect    Connected is for any events happen on service store site of any partner, like member purchasing a membership
        $this->event_for = $request->has('connect') ? 'connected' : 'local'; 
        $secret = $request->has('connect') ? config('services.stripe.endpoint_secret_connect') : config('services.stripe.endpoint_secret');

        try {
            $event = Webhook::constructEvent(
                $payload,
                $sig_header,
                $secret
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
            'connected_account' => $event_data->account ?? null,
            'type' => $event_data->type,
            'event_for' => $this->event_for,
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
        $method_name = Str::camel(str_replace('.', '_', $event_type)); //Convert type into camel case, string helper works great converting snake to camel: 'checkout.session.completed' => checkoutSessionCompleted() or 'payment_intent.succeeded' => paymentIntentSucceeded()

        return method_exists($this->service, $method_name) ? 
            $this->service->{ $method_name }($stripe_event) :
            new Response("Unsupported type: {$event_type}", 200);
    }
}
