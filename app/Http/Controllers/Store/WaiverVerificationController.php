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
        $waiver = Waiver::where('show_at', 'sign-up')->where('is_active', 1)->first();
        if(!$waiver) {
            return redirect(route('ss.member.dashboard', ["subdomain" => request()->session()->get('business_settings')['subdomain']]));
        }
        if($waiver) {
            $user_waiver = UserWaiver::where('user_id', auth()->user()->id)
            ->where('waiver_id', $waiver->id)->first();
            if($user_waiver) {
                return redirect(route('ss.member.dashboard', ["subdomain" => request()->session()->get('business_settings')['subdomain']]));
            }
        }
        return Inertia::render('Store/WaiverVerification', [
            'page_title' => __('Waiver Verification'),
            'waiver' => $waiver,
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
