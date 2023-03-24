<?php

namespace App\Services\Shared\Aws;

use Aws\Lightsail\LightsailClient;
use Aws\Lightsail\Exception\LightsailException;
// use Aws\Result;
use \Exception;

class LightsailService
{
    private $client;

    public function __construct()
    {
        $this->client = new LightsailClient(config('services.lightsail'));
        $this->response = (object) [
            'error' => false,
            'error_message' => null,
            'data' => null,
            'status_code' => null,
            'error_code' => null
        ];
    }

    public function setError($e)
    {
        $this->response->error = true;

        if($e instanceof LightsailException){
            $this->response->error_message = $e->getAwsErrorMessage();
            $this->response->status_code = $e->getStatusCode();
            $this->response->error_code = $e->getAwsErrorCode();
            return;
        }

        $this->response->error_message = $e->getMessage();
        $this->response->status_code = $e->getCode();
    }

    public function call($method, $arguments = []) : object
    {
        try {
            $response = $this->client->{$method}(...$arguments);
        } catch(LightsailException $e) {
            $this->setError($e);
        } catch (Exception $e) {
            $this->setError($e);
        }

        $this->response->data = !empty($response) ? $response->toArray() : null;

        return $this->response;
    }
}
