<?php

namespace App\Services\Shared;

class StripePaymentService extends StripeService
{
    // Method to create new Stripe\Checkout\Session for connected account
    public function createCheckoutSession($connected_account_id, $data) : object
    {
        $endpoint = [
            'checkout',
            'sessions'
        ];
        $action = 'create';
        $stripe_account = ['stripe_account' => $connected_account_id];

        return $this->call($endpoint, $action, [$data, $stripe_account]);
    }
}
