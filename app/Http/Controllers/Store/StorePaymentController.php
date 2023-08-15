<?php

namespace App\Http\Controllers\Store;

use App\Enums\StripePriceType;
use App\Http\Controllers\Controller;
use App\Models\Partner\Order;
use App\Models\Partner\PackPrice;
use App\Services\Shared\Stripe\CustomerService;
use App\Services\Shared\Stripe\PaymentService;
use Inertia\Inertia;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\URL;

class StorePaymentController extends Controller
{
    public function __construct(PaymentService $stripe_payment_service, CustomerService $stripe_customer_service)
    {
        $this->stripe_payment_service = $stripe_payment_service;
        $this->stripe_customer_service = $stripe_customer_service;
    }

    /**
     * index.
     * 
     * @param string $subdomain
     * @param PackPrice $price
     *
     * @return \Illuminate\Http\Response
     */
    //2023-08-15 moved this route under auth middleware
    public function index(Request $request, $subdomain, PackPrice $price)
    {
        $msg = __('This option is no longer available.');
        //make sure the price is active
        if(!$price->is_active){
            return $this->redirectBackError($msg);
        }
        //make sure the parent pack is active also
        $pack = $price->priceable;
        if(!$pack->is_active){
            return $this->redirectBackError($msg);
        }

        $business = $this->business();
        $stripe_account_id = $business->stripe_account_id ?? null;
        if(!$business || empty($stripe_account_id)){
            $msg = __('This store temporarily can\'t accept payments.');
            return $this->redirectBackError($msg);
        }

        // returns null or customer stripe id (cus_), also attempts to create new customer.
        $stripe_customer = $this->stripe_customer_service->getCustomer();
        // abort checkout if we can't create a customer, we don't want any guest customers created
        if(empty($stripe_customer)){
            $msg = __('Please contact support for help.');
            return $this->redirectBackError($msg);
        }

        $connected_account_id = $this->business()->stripe_account_id;
        //below string will be part of success return url, 'client_reference_id' prop will be set on checkout session (webhook use) and captured on Order
        $trace = (string) Str::uuid();
        $success_url = URL::temporarySignedRoute('ss.payments.success', now()->addMinutes(30), ['subdomain' => $subdomain, 'trace' => $trace]);
        $data = array(
            'mode' => StripePriceType::mode($price->type), // one of: one_time => payment; recurring => subscription; setup => setup;
            'success_url' => $success_url,
            'cancel_url' => route('ss.memberships.index', [$subdomain]),
            'client_reference_id' => $trace,
            'customer' => $stripe_customer,
        );

        if($price->type == 'one_time'){
            $data['payment_intent_data'] = array(
                'application_fee_amount' => round($price->unit_amount * 0.025) //DEMO 2.5%
                //The amount of the application fee (if any) that will be requested to be applied to the payment and transferred to the application owner’s Stripe account. The amount of the application fee collected will be capped at the total payment amount.
            );
        }

        if($price->type == 'recurring'){
            $data['subscription_data'] = array(
                'application_fee_percent' => 5, //DEMO 5%
                // A non-negative decimal between 0 and 100, with at most two decimal places. This represents the percentage of the subscription invoice subtotal that will be transferred to the application owner’s Stripe account.
            );
        }

        if(in_array($price->type, ['one_time', 'recurring'])){
            $data['line_items'] = array(
                [
                    'price' => $price->stripe_price_id,
                    'quantity' => 1,
                ],
            );
            //Note: checkout session object won't have line items. We can call API and retrive line items (with 'expand' => ['line_items'])
        }

        //create checkout session
        $result = $this->stripe_payment_service->createCheckoutSession($connected_account_id, $data);
        
        // return error or redirect to checkout
        return $result->error ? $this->redirectBackError($result->error_message) : Inertia::location($result->data->url);
    }

    public function success(Request $request, $subdomain)
    {
        if(!$request->hasValidSignature()){
            return redirect()->route('ss.home', $subdomain);
        }

        //request must have a trace from url query!
        $trace = $request->trace;
        //check if order has been created from webhook event
        $order = Order::where('trace', $trace)->first();

        //TODO
        //if order has been created, show order contents with a summary
        dump($order);

        return Inertia::render('Store/Success', [
            'page_title' => __('Success'),
            'header' => __('Success'),
        ]);
    }
}
