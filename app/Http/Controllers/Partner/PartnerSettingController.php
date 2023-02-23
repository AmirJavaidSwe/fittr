<?php

namespace App\Http\Controllers\Partner;

use App\Services\Partner\PartnerSettingService;
use App\Enums\SettingGroup;
use App\Http\Controllers\Controller;
use App\Http\Requests\Partner\SettingsGeneralDetailsRequest;
use App\Models\Country;
use App\Models\Timezone;
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
                    'title' => __('Business Settings'),
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
                    'title' => __('Business Settings'),
                    'link' => null,
                ],
            )
        ]);
    }

    // Business Settings column / General Settings / Date time formats
    public function generalFormats(Request $request)
    {
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
                    'title' => __('Business Settings'),
                    'link' => null,
                ],
            )
        ]);
    }
}
