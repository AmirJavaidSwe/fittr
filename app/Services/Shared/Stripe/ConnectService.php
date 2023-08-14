<?php

namespace App\Services\Shared\Stripe;

use App\Models\Country;
use Inertia\Inertia;
use App\Traits\GenericHelper;
use Illuminate\Http\Response;
use Illuminate\Http\RedirectResponse;

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
class ConnectService extends StripeService
{
    use GenericHelper;

    public $account_standard_type;
    public function __construct()
    {
        $this->account_standard_type = 'standard';
        parent::__construct();
    }

    public function createStandardConnectedAccount($partner, $prefilled = []) : object
    {
        $business = session('business');
        $data = array(
            'type' => $this->account_standard_type, //May be one of custom, express or standard. Standard type is used at all times.
            'email' => $partner->email, //The email address of the account holder. This is only to make the account easier to identify to you.
            // 'capabilities' => [
            //   'card_payments' => ['requested' => true],
            // ],
            'business_type' => 'company',
            'metadata' => [
                'business_id' => $business->id,
                'partner_id' => $partner->id,
            ],
        );

        //pass relavant details prefilled
        $country = !empty($prefilled['country_id']) ? Country::find($prefilled['country_id']) : null;
        if($country){
            $data['country'] = $country->iso;
            $data['company']['address']['country'] = $country->iso;
        }
        // if(!empty($prefilled['business_email']) ){
        //     $data['email'] = $prefilled['business_email'];
        // }
        
        if(!empty($prefilled['business_name'])){
            $data['company']['name'] = $prefilled['business_name'];
        }
        if(!empty($prefilled['business_phone']) && $country){
            $data['company']['phone'] = $country->dial_code.$prefilled['business_phone'];
        }
        if(!empty($prefilled['address_line1'])){
            $data['company']['address']['line1'] = $prefilled['address_line1'];
        }
        if(!empty($prefilled['address_line2'])){
            $data['company']['address']['line2'] = $prefilled['address_line2'];
        }
        if(!empty($prefilled['city'])){
            $data['company']['address']['city'] = $prefilled['city'];
        }
        if(!empty($prefilled['zip_code'])){
            $data['company']['address']['postal_code'] = $prefilled['zip_code'];
        }
        if(!empty($prefilled['state'])){
            $data['company']['address']['state'] = $prefilled['state'];
        }

        $endpoint = 'accounts';
        $action = 'create';
        $this->call($endpoint, $action, [$data]);

        if($this->response->error === false){
            $business->stripe_account_id = $this->response?->data->id;
            $business->save();

            //update session business
            session(['business' => $business]);
        }

        return $this->response;
    }

    /*
        Note: You must update your Connect branding settings with icon, brand color in order to create an account link.
    */
    public function generateLink($stripe_account_id) : ?string
    {
        $endpoint = 'accountLinks';
        $action = 'create';
        $data = [
            'account' => $stripe_account_id,
            'refresh_url' => route('partner.settings.payments.stripe', ['reauth']),
            'return_url' => route('partner.settings.payments.stripe'),
            'type' => 'account_onboarding',
        ];
        $this->call($endpoint, $action, [$data]);

        return $this->response->data?->url;
    }

    public function showConnectLink($url = null) : Response|RedirectResponse
    {
        return $url ? Inertia::location($url) : $this->redirectBackWarning(__('Unable to generate link'));
    }

    public function generateAndShowLink($stripe_account_id)
    {
        $url = $this->generateLink($stripe_account_id);

        return $this->showConnectLink($url);
    }

    public function retrieveAccount($stripe_account_id = null) : ?object
    {
        if(empty($stripe_account_id)){
            return $this->response;
        }
        $endpoint = 'accounts';
        $action = 'retrieve';
        $data = $stripe_account_id;
        $this->call($endpoint, $action, [$data]);

        return $this->response;
    }
}
