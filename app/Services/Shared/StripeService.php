<?php

namespace App\Services\Shared;

use Stripe\StripeClient;

class StripeService
{
    public function __construct()
    {
        $this->stripe = new StripeClient(config('services.stripe.secret_key'));
        $this->response = (object) ['error' => false, 'error_message' => null, 'data' => null, 'code' => null];
    }

    public function setError($e)
    {
        $this->response->error = true;
        $this->response->error_message = $e->getMessage();
        $this->response->code = $e->getCode();
    }

    public function endpoint($endpoint)
    {
        if(!is_array($endpoint)){
            return $this->stripe->{$endpoint};
        }
        //required to set correct services with chaining for structured endpoints, e.g. $this->stripe->checkout->sessions->...
        foreach($endpoint as $path){
            $this->stripe = $this->stripe->{$path};
        }
        return $this->stripe;
    }

    public function call($endpoint, $action, $arguments) :object
    {
        try {
            $response = $this->endpoint($endpoint)->{$action}(...$arguments);
        } catch(\Stripe\Exception\CardException $e) {
            $this->setError($e);
        } catch (\Stripe\Exception\RateLimitException $e) {
            $this->setError($e);
        } catch (\Stripe\Exception\InvalidRequestException $e) {
            $this->setError($e);
        } catch (\Stripe\Exception\AuthenticationException  $e) {
            $this->setError($e);
        } catch (\Stripe\Exception\ApiConnectionException $e) {
            $this->setError($e);
        } catch (\Stripe\Exception\ApiErrorException $e) {
            $this->setError($e);
        } catch (\Exception $e) {
            $this->setError($e);
        }
        $this->response->data = $response ?? null;

        return $this->response;
    }

}
