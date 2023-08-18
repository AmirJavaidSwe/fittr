<?php

namespace App\Services\Shared\Stripe;

class CustomerService extends StripeService
{
    // Returns logged in user stripe_id or null
    // Method will try to create new customer if needed for a user
    public function getCustomer() : ?string
    {
        $user = auth()->user();
        if (!$user) {
            return null;
        }
        if (!empty($user->stripe_id)) {
            return $user->stripe_id;
        }
        //attempt creating a customer
        $result = $this->createStripeCustomer($user);
        $stripe_id = null;

        if ($result->error === false && !empty($result->data)) {
            $stripe_id = $result->data?->id;
        }
        //update stripe_id on user
        if(!empty($stripe_id)){
            $user->update(['stripe_id' => $stripe_id]);
        }

        return $stripe_id;
    }

    public function createStripeCustomer($user)
    {
        $business = session('business');
        $connected_account_id = $business->stripe_account_id;
        $data = array(
            'email' => $user->email,
            'name' => $user->name,
            'metadata' => [
                'business_id' => $business->id,
                'user_id' => $user->id
            ],
        );

        $endpoint = 'customers';
        $action = 'create';
        $stripe_account = ['stripe_account' => $connected_account_id];

        return $this->call($endpoint, $action, [$data, $stripe_account]);
    }
}
