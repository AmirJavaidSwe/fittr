<?php

namespace App\Services\Shared\Stripe;

class InvoiceService extends StripeService
{
    // $connected_account_id = $stripe_event->connected_account;
    // $params = ['expand' => ['subscription']];
    // $remote = $this->stripe_invoice_service->retrieveInvoice($connected_account_id, $stripe_event->data->invoice);
    public function retrieveInvoice($connected_account_id, $id, $params = null) : ?object
    {
        $endpoint = 'invoices';
        $action = 'retrieve';
        $stripe_account = ['stripe_account' => $connected_account_id];
        
        // sdk public function retrieveSource($parentId, $id, $params = null, $opts = null)
        $this->call($endpoint, $action, [$id, $params, $stripe_account]); //$id, $params = null, $opts = null

        return $this->response;
    }
}
