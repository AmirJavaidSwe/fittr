<?php

namespace App\Http\Controllers\Store;

use Inertia\Inertia;
use Illuminate\Http\Request;
use App\Models\Partner\Waiver;
use App\Models\Partner\UserWaiver;
use App\Http\Controllers\Controller;
use App\Models\Partner\FamilyMember;
use App\Services\Partner\WaiverValidationAndSaveService;

class WaiverVerificationController extends Controller
{
    public function index(Request $request)
    {
        $waivers = Waiver::where('show_at', 'sign-up')->where('is_active', 1)->get();

        if ($waivers->isEmpty()) {
            return redirect(route('ss.member.dashboard', ["subdomain" => request()->session()->get('business_settings')['subdomain']]));
        }

        $user = auth()->user();
        $userHasAcceptedAnyWaiver = false;

        foreach ($waivers as $waiver) {
            $user_waiver = UserWaiver::where('user_id', $user->id)
                ->where('waiver_id', $waiver->id)
                ->first();

            if ($user_waiver) {
                $userHasAcceptedAnyWaiver = true;
                break;
            }
        }
        if ($userHasAcceptedAnyWaiver) {
            return redirect(route('ss.member.dashboard', ["subdomain" => request()->session()->get('business_settings')['subdomain']]));
        }

        return Inertia::render('Store/WaiverVerification', [
            'page_title' => __('Waiver Verification'),
            'waivers' => $waivers,
            'user' => $user,
        ]);
    }
    public function store(Request $request)
    {
        $waiverValidation = WaiverValidationAndSaveService::validateIfHasWaiver();

        if(!empty($waiverValidation)) {
            return back()->withErrors($waiverValidation)->withInput();
        }
        WaiverValidationAndSaveService::saveWaiverAcceptanceData();

        return redirect()->intended();
    }
}
