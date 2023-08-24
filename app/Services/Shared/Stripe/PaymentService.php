<?php

namespace App\Services\Shared\Stripe;

class PaymentService extends StripeService
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

    public function retrieveCheckoutSession($connected_account_id, $id, $params = null) : ?object
    {
        $endpoint = [
            'checkout',
            'sessions'
        ];
        $action = 'retrieve';
        $stripe_account = ['stripe_account' => $connected_account_id];
        
        // sdk public function retrieveSource($parentId, $id, $params = null, $opts = null)
        $this->call($endpoint, $action, [$id, $params, $stripe_account]); //$id, $params = null, $opts = null

        return $this->response;
    }
}
