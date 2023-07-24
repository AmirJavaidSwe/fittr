<?php

namespace App\Services\Shared;

use App\Enums\StripePriceType;

/*
    $this->call($endpoint, $action, [$param]);
    is a wrapper method to call Stripe API as such:
    $this->stripe->{$endpoint}->{$action}(...$arguments)

    Example 1: (POST, no id)
    $this->call('accounts', 'create', []); => will resolve into: 
    $this->stripe->accounts->create([])

    Example 2: (GET, with id)
    $this->call('accounts', 'retrieve', 'acct_1MlS3u2ayttmglZD'); => will resolve into: 
    $this->stripe->accounts->retrieve('acct_1MlS3u2ayttmglZD')

    Example 3: (GET, with multiple arguments)
    $this->call('customers', 'retrievePaymentMethod', ['cus_NWGv1hiFyDqPRX', 'pm_1MlENFBa0RDFhD3VneOdY18x']); => will resolve into: 
    $this->stripe->customers->retrievePaymentMethod('acct_1MlS3u2ayttmglZD', 'pm_1MlENFBa0RDFhD3VneOdY18x')

    *When ->call() used within class that extends StripeService, $this->response object will be available as well as returned result from this method otherwise.
*/
class StripeProductService extends StripeService
{
    //PACK methods:

    public function createOrUpdatePackProduct($connected_account_id, $pack) : object
    {
        //create new stripe product if pack has no stripe_product_id
        $product_data = $this->getPackData($pack);

        if(empty($pack->stripe_product_id)) {
            $attempt = $this->createProduct($connected_account_id, $product_data);
            $pack->update(['stripe_product_id' => $attempt->data?->id]);
            
            return $attempt;
        }

        //update existing product only if below fields have changed
        $was_changed = $pack->wasChanged(['is_active', 'title']);
        if($was_changed){
            $attempt = $this->updateProduct($connected_account_id, $pack->stripe_product_id, $product_data);

            return $attempt;
        }

        // return base object
        return $this->response;
    }

    // Helper method that returns formed array to be used for product create/update
    public function getPackData($pack) : array
    {
        return array(
            'name' => $pack->title,
            'active' => $pack->is_active,
            'shippable' => false,
            'metadata' => [
                'pack_id' => $pack->id,
                'type' => $pack->type,
            ],
        );
    }

    // Method to create new Stripe product
    public function createProduct($connected_account_id, $product_data) : object
    {
        $endpoint = 'products';
        $action = 'create';
        $stripe_account = ['stripe_account' => $connected_account_id];

        return $this->call($endpoint, $action, [$product_data, $stripe_account]);
    }

    // Method to update Stripe product (active product state and name supported only)
    public function updateProduct($connected_account_id, $stripe_product_id, $product_data) : object
    {
        $endpoint = 'products';
        $action = 'update';
        $stripe_account = ['stripe_account' => $connected_account_id];

        return $this->call($endpoint, $action, [$stripe_product_id, $product_data, $stripe_account]);
    }

    //PACK PRICE methods:
    public function createPackPrice($params) : object
    {
        extract($params); // [$pack, $validated_data, $currency, $currency_symbol, $connected_account_id]

        $price_data = array(
            'currency' => $currency,
            'product' => $pack->stripe_product_id,
            'unit_amount' => $validated_data['price'] * 100,
            'active' => $validated_data['is_active'],
            // 'tax_behavior' => '',// One of inclusive, exclusive, or unspecified
            'metadata' => [
                'pack_id' => $pack->id,
            ],
        );

        if($validated_data['type'] == StripePriceType::recurring->name){
            $price_data['recurring']['interval'] = $validated_data['interval'];
            $price_data['recurring']['interval_count'] = $validated_data['interval_count'];
        }

        $attempt = $this->createPrice($connected_account_id, $price_data);
        if($attempt->error){
            return $attempt;
        }

        //Create new model
        $model_data = [
            'stripe_price_id' => $attempt->data?->id,
            'unit_amount' => $price_data['unit_amount'],
            'currency' => $price_data['currency'],
            'currency_symbol' => $currency_symbol,
        ] + $validated_data;
        //create new price
        $price = $pack->prices()->create($model_data);
        //sync location restrictions
        $price->locations()->sync($validated_data['location_ids'] ?? []);

        return $attempt;
    }

    // API Method to create new Stripe price
    public function createPrice($connected_account_id, $price_data) : object
    {
        $endpoint = 'prices';
        $action = 'create';
        $stripe_account = ['stripe_account' => $connected_account_id];

        return $this->call($endpoint, $action, [$price_data, $stripe_account]);
    }

    // API Method to update Stripe price
    public function updatePrice($connected_account_id, $stripe_price_id, $price_data) : object
    {
        $endpoint = 'prices';
        $action = 'update';
        $stripe_account = ['stripe_account' => $connected_account_id];

        return $this->call($endpoint, $action, [$stripe_price_id, $price_data, $stripe_account]);
    }
    
    // API Method to list all prices, $query array can be used to pull prices for particular product, filter by active state, currency and other parameters
    public function listPrices($connected_account_id, $query) : object
    {
        $endpoint = 'prices';
        $action = 'all';
        $stripe_account = ['stripe_account' => $connected_account_id];
        //sample of a query:
        // $query = array(
        //     'product' => 'prod_XYZ',
        //     'active' => true,
        // );

        return $this->call($endpoint, $action, [$query, $stripe_account]);
    }
}
