<?php

namespace App\Http\Controllers\Store;

use App\Enums\StripePriceType;
use App\Http\Controllers\Controller;
use App\Models\Partner\PackPrice;
use App\Services\Shared\Stripe\PaymentService;
use Inertia\Inertia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;

class StorePaymentController extends Controller
{
    public function __construct(PaymentService $stripe_payment_service)
    {
        $this->stripe_payment_service = $stripe_payment_service;
    }

    /**
     * index.
     * 
     * @param string $subdomain
     *
     * @return \Illuminate\Http\Response
     */
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

        $connected_account_id = $this->business()->stripe_account_id;
        $success_url = URL::temporarySignedRoute('ss.payments.success', now()->addMinutes(30), [$subdomain]);
        $data = array(
            'mode' => StripePriceType::mode($price->type), // one of: payment - onetime; setup - save payment details; subscription
            'line_items' => [
                [
                    'price' => $price->stripe_price_id,
                    'quantity' => 1,
                ],
            ],
            'success_url' => $success_url,
            'cancel_url' => route('ss.memberships.index', [$subdomain]),
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

        return Inertia::render('Store/Success', [
            'page_title' => __('Success'),
            'header' => __('Success'),
        ]);
    }
}
