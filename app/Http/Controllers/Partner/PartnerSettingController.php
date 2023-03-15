<?php

namespace App\Http\Controllers\Partner;

use App\Services\Partner\PartnerSettingService;
use App\Services\Shared\StripeConnectService;
use App\Enums\FormatType;
use App\Enums\SettingKey;
use App\Enums\SettingGroup;
use App\Http\Controllers\Controller;
use App\Http\Requests\Partner\SettingsGeneralDetailsRequest;
use App\Http\Requests\Partner\SettingsGeneralAddressRequest;
use App\Http\Requests\Partner\SettingsGeneralFormatsRequest;
use App\Http\Requests\Partner\SettingsServiceStoreGeneralRequest;
use App\Http\Requests\Partner\SettingsServiceStoreHeaderRequest;
use App\Http\Requests\Partner\SettingsServiceStoreSeoRequest;
use App\Http\Requests\Partner\SettingsServiceStoreWaiversRequest;
use App\Models\Country;
use App\Models\Timezone;
use App\Models\Format;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Storage;


class PartnerSettingController extends Controller
{
    public function __construct(PartnerSettingService $service, StripeConnectService $stripe_connect_service)
    {
        $this->service = $service;
        $this->stripe_connect_service = $stripe_connect_service;
    }

    public function index(Request $request)
    {
        return Inertia::render('Partner/Settings', [
            'page_title' => __('Settings'),
            'header' => __('Settings'),
        ]);
    }

    /* General */
    /* Business and operations settings */

    // Business Settings column / General Settings / Business Details
    public function generalDetails(Request $request)
    {
        return Inertia::render('Partner/Settings/BusinessDetails', [
            'page_title' => __('Business Settings - Business Details'),
            'header' => array(
                [
                    'title' => __('Settings'),
                    'link' => route('partner.settings.index'),
                ],
                [
                    'title' => '/',
                    'link' => null,
                ],
                [
                    'title' => __('Business Details'),
                    'link' => null,
                ],
            ),
            'countries' => Country::where('status', true)->get(),
            'timezones' => Timezone::where('status', true)->orderBy('title', 'asc')->get(),
            'form_data' => $this->service->getByGroup(SettingGroup::general_details),
        ]);
    }

    public function generalDetailsUpdate(SettingsGeneralDetailsRequest $request)
    {
        $this->service->update($request);

        return $this->redirectBackSuccess(__('Settings saved'));
    }

    // Business Settings column / General Settings / Legal Address
    public function generalAddress(Request $request)
    {
        // $form_keys = array(
        //     SettingKey::address_line1->name,
        //     SettingKey::address_line2->name,
        //     SettingKey::city->name,
        //     SettingKey::state->name,
        //     SettingKey::zip_code->name,
        //     SettingKey::legal_country_id->name,
        // );
        return Inertia::render('Partner/Settings/LegalAddress', [
            'page_title' => __('Business Settings - Legal Address'),
            'header' => array(
                [
                    'title' => __('Settings'),
                    'link' => route('partner.settings.index'),
                ],
                [
                    'title' => '/',
                    'link' => null,
                ],
                [
                    'title' => __('Legal Address'),
                    'link' => null,
                ],
            ),
            'countries' => Country::where('status', true)->get(),
            // 'form_data' => $this->service->getByKeys($form_keys), //for ref: alternative method to pull data by key
            'form_data' => $this->service->getByGroup(SettingGroup::general_address),
        ]);
    }

    public function generalAddressUpdate(SettingsGeneralAddressRequest $request)
    {
        $this->service->update($request);

        return $this->redirectBackSuccess(__('Settings saved'));
    }

    // Business Settings column / General Settings / Date time formats
    public function generalFormats(Request $request)
    {
        $formats = Format::all();
        return Inertia::render('Partner/Settings/Formats', [
            'page_title' => __('Business Settings - Formats'),
            'header' => array(
                [
                    'title' => __('Settings'),
                    'link' => route('partner.settings.index'),
                ],
                [
                    'title' => '/',
                    'link' => null,
                ],
                [
                    'title' => __('Formats'),
                    'link' => null,
                ],
            ),
            'formats_date' => $formats->where('type', FormatType::date->name)->values(), //!reset keys for json
            'formats_time' => $formats->where('type', FormatType::time->name)->values(), //!reset keys for json
            'form_data' => $this->service->getByGroup(SettingGroup::general_formats),
        ]);
    }

    public function generalFormatsUpdate(SettingsGeneralFormatsRequest $request)
    {
        $this->service->update($request);

        return $this->redirectBackSuccess(__('Settings saved'));
    }

    /* Service store */
    /* Customise web store looks and features */

    // Online Store column / Service store / General
    public function serviceStoreGeneral(Request $request)
    {
        return Inertia::render('Partner/Settings/ServiceStoreGeneral', [
            'page_title' => __('Settings - Service store - General'),
            'header' => array(
                [
                    'title' => __('Settings'),
                    'link' => route('partner.settings.index'),
                ],
                [
                    'title' => '/',
                    'link' => null,
                ],
                [
                    'title' => __('Service store'),
                    'link' => null,
                ],
            ),
            'form_data' => $this->service->getByGroup(SettingGroup::service_store_general),
        ]);
    }

    public function serviceStoreGeneralUpdate(SettingsServiceStoreGeneralRequest $request)
    {
        $this->service->update($request);

        return $this->redirectBackSuccess(__('Settings saved'));
    }

    // Online Store column / Service store / Header & Footer
    public function serviceStoreHeader(Request $request)
    {
        $props = $this->service->getByKeys([SettingKey::logo->name, SettingKey::favicon->name]);
        $logo = !empty($props[SettingKey::logo->name]) ? Storage::disk('public')->url($props[SettingKey::logo->name]) : null;
        $favicon = !empty($props[SettingKey::favicon->name]) ? Storage::disk('public')->url($props[SettingKey::favicon->name]) : null;

        return Inertia::render('Partner/Settings/ServiceStoreHeader', [
            'page_title' => __('Settings - Service store - Header & Footer'),
            'header' => array(
                [
                    'title' => __('Settings'),
                    'link' => route('partner.settings.index'),
                ],
                [
                    'title' => '/',
                    'link' => null,
                ],
                [
                    'title' => __('Service store'),
                    'link' => null,
                ],
            ),
            'logo' => $logo,
            'favicon' => $favicon,
            'form_data' => $this->service->getByGroup(SettingGroup::service_store_header),
        ]);
    }

    public function serviceStoreHeaderUpdate(SettingsServiceStoreHeaderRequest $request)
    {
        $this->service->update($request);

        return $this->redirectBackSuccess(__('Settings saved'));
    }

    // Online Store column / Service store / Seo
    public function serviceStoreSeo(Request $request)
    {
        return Inertia::render('Partner/Settings/ServiceStoreSeo', [
            'page_title' => __('Settings - Service store - SEO'),
            'header' => array(
                [
                    'title' => __('Settings'),
                    'link' => route('partner.settings.index'),
                ],
                [
                    'title' => '/',
                    'link' => null,
                ],
                [
                    'title' => __('Service store'),
                    'link' => null,
                ],
            ),
            'form_data' => $this->service->getByGroup(SettingGroup::service_store_seo),
        ]);
    }

    public function serviceStoreSeoUpdate(SettingsServiceStoreSeoRequest $request)
    {
        $this->service->update($request);

        return $this->redirectBackSuccess(__('Settings saved'));
    }

    // Online Store column / Waivers
    public function serviceStoreWaivers(Request $request)
    {
        return Inertia::render('Partner/Settings/ServiceStoreWaivers', [
            'page_title' => __('Settings - Service store - Waivers'),
            'header' => array(
                [
                    'title' => __('Settings'),
                    'link' => route('partner.settings.index'),
                ],
                [
                    'title' => '/',
                    'link' => null,
                ],
                [
                    'title' => __('Waivers'),
                    'link' => null,
                ],
            ),
            'default_waiver' => $this->service->getDefaultData('default_waiver'),
            'form_data' => $this->service->getByGroup(SettingGroup::service_store_waivers),
        ]);
    }

    public function serviceStoreWaiversUpdate(SettingsServiceStoreWaiversRequest $request)
    {
        $this->service->update($request);

        return $this->redirectBackSuccess(__('Settings saved'));
    }

    /* Bookings & Payments */
    // Bookings & Payments column / Payments => List of available payment gateways
    public function payments(Request $request)
    {
        return Inertia::render('Partner/Settings/Payments', [
            'page_title' => __('Settings - Bookings & Payments - Payments'),
            'header' => array(
                [
                    'title' => __('Settings'),
                    'link' => route('partner.settings.index'),
                ],
                [
                    'title' => '/',
                    'link' => null,
                ],
                [
                    'title' => __('Payments'),
                    'link' => null,
                ],
            ),
        ]);
    }

    // Bookings & Payments column / Payments / Stripe
    public function paymentsStripe(Request $request)
    {
        //see: $this->stripe_connect_service->generateLink($stripe_account_id)
        if($request->has('reauth')){
            return $this->connectStripe($request);
        }

        $partner = $request->user();

        return Inertia::render('Partner/Settings/PaymentsStripe', [
            'page_title' => __('Settings - Bookings & Payments - Stripe Payments'),
            'header' => array(
                [
                    'title' => __('Settings'),
                    'link' => route('partner.settings.index'),
                ],
                [
                    'title' => '/',
                    'link' => null,
                ],
                [
                    'title' => __('Payments'),
                    'link' => route('partner.settings.payments'),
                ],
                [
                    'title' => '/',
                    'link' => null,
                ],
                [
                    'title' => 'Stripe',
                    'link' => null,
                ],
            ),
            'has_account' => $partner->has_stripe_account,
            'stripe_account' => $this->stripe_connect_service->retrieveAccount($partner->stripe_account_id)->data,
        ]);
    }

    //POST
    public function connectStripe(Request $request): Response|RedirectResponse
    {
        // $gate = Gate::allowIf(fn (User $user) => $user->is_partner && ! ALREADY CONNECTED);  charges_enabled

        $partner = $request->user();
        //User has connected account but onboarding not complete
        if($partner->has_stripe_account){
            return $this->stripe_connect_service->generateAndShowLink($partner->stripe_account_id);
        }

        //creates new account and updates $partner->stripe_account_id on success; Returns service object
        $general_details = $this->service->getByGroup(SettingGroup::general_details);
        $general_address = $this->service->getByGroup(SettingGroup::general_address);
        $prefilled = $general_address + $general_details;
        $result = $this->stripe_connect_service->createStandardConnectedAccount($partner, $prefilled);
        //redirect back if any errors
        if($result->error || empty($result->data->id)){
            return $this->redirectBackError($result->error_message);
        }

        return $this->stripe_connect_service->generateAndShowLink($result->data->id);
    }
}