<?php

namespace App\Services\Shared\Stripe;

use Stripe\StripeClient;
use Stripe\Exception\CardException;
use Stripe\Exception\RateLimitException;
use Stripe\Exception\InvalidRequestException;
use Stripe\Exception\AuthenticationException;
use Stripe\Exception\ApiConnectionException;
use Stripe\Exception\ApiErrorException;

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
        } catch(CardException $e) {
            $this->setError($e);
        } catch (RateLimitException $e) {
            $this->setError($e);
        } catch (InvalidRequestException $e) {
            $this->setError($e);
        } catch (AuthenticationException $e) {
            $this->setError($e);
        } catch (ApiConnectionException $e) {
            $this->setError($e);
        } catch (ApiErrorException $e) {
            $this->setError($e);
        } catch (\Exception $e) {
            $this->setError($e);
        }
        $this->response->data = $response ?? null;

        return $this->response;
    }

}
