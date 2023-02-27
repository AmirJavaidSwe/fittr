<?php

namespace App\Http\Controllers\Partner;

use App\Services\Partner\PartnerSettingService;
use App\Enums\FormatType;
// use App\Enums\SettingKey;
use App\Enums\SettingGroup;
use App\Http\Controllers\Controller;
use App\Http\Requests\Partner\SettingsGeneralDetailsRequest;
use App\Http\Requests\Partner\SettingsGeneralAddressRequest;
use App\Http\Requests\Partner\SettingsGeneralFormatsRequest;
use App\Models\Country;
use App\Models\Timezone;
use App\Models\Format;
use Illuminate\Http\Request;
use Inertia\Inertia;

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
}
