<?php

namespace App\Http\Controllers\Partner;

use App\Services\Partner\PartnerSettingService;
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
use Inertia\Inertia;
use Storage;

class PartnerSettingController extends Controller
{
    public function __construct(PartnerSettingService $service)
    {
        $this->service = $service;
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

        return redirect()->back()->with('flash_type', 'success')->with('flash_message', __('Settings saved'))->with('flash_timestamp', time());
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

        return redirect()->back()->with('flash_type', 'success')->with('flash_message', __('Settings saved'))->with('flash_timestamp', time());
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

        return redirect()->back()->with('flash_type', 'success')->with('flash_message', __('Settings saved'))->with('flash_timestamp', time());
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

        return redirect()->back()->with('flash_type', 'success')->with('flash_message', __('Settings saved'))->with('flash_timestamp', time());
    }

    // Online Store column / Service store / General
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

        return redirect()->back()->with('flash_type', 'success')->with('flash_message', __('Settings saved'))->with('flash_timestamp', time());
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

        return redirect()->back()->with('flash_type', 'success')->with('flash_message', __('Settings saved'))->with('flash_timestamp', time());
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

        return redirect()->back()->with('flash_type', 'success')->with('flash_message', __('Settings saved'))->with('flash_timestamp', time());
    }
}
