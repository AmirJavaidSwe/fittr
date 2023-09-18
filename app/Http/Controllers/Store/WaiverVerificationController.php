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
        $userHasAcceptedAnyWaiver = 0;

        foreach ($waivers as $waiver) {
            $user_waiver = UserWaiver::where('user_id', $user->id)
                ->where('waiver_id', $waiver->id)
                ->first();

            if ($user_waiver != null) {
                $userHasAcceptedAnyWaiver++;
            }
        }
        $user_waivers = UserWaiver::where('user_id', $user->id)
        ->get();
        if ($userHasAcceptedAnyWaiver == count($waivers)) {
            return Inertia::render('Store/WaiverVerification', [
                'page_title' => __('Waiver Verification'),
                'waivers' => $waivers,
                'user' => $user,
                'user_waivers' => $user_waivers
            ]);
        }

        return Inertia::render('Store/WaiverVerification', [
            'page_title' => __('Waiver Verification'),
            'waivers' => $waivers,
            'user' => $user,
            'user_waivers' => $user_waivers
        ]);
    }
    public function store(Request $request)
    {
        $waiverValidation = WaiverValidationAndSaveService::validateIfHasWaiver();

        if(!empty($waiverValidation)) {
            return back()->withErrors($waiverValidation)->withInput();
        }
        WaiverValidationAndSaveService::saveWaiverAcceptanceData();
        $waivers = Waiver::where('show_at', 'sign-up')->where('is_active', 1)->get();
        $user = auth()->user();
        $userHasAcceptedAnyWaiver = 0;
        foreach ($waivers as $waiver) {
            $user_waiver = UserWaiver::where('user_id', $user->id)
                ->where('waiver_id', $waiver->id)
                ->first();

            if ($user_waiver != null) {
                $userHasAcceptedAnyWaiver++;
            }
        }
        if(count($waivers) == $userHasAcceptedAnyWaiver){
            $user_waivers = UserWaiver::where('user_id', $user['id'])->get();
            return Inertia::render('Store/WaiverVerification', [
                'page_title' => __('Waiver Verification'),
                'waivers' => $waivers,
                'user' => $user,
                'user_waivers' => $user_waivers
            ]);
        }else{
            $user_waivers = UserWaiver::where('user_id', $user['id'])->get();
            return Inertia::render('Store/WaiverVerification', [
                'page_title' => __('Waiver Verification'),
                'waivers' => $waivers,
                'user' => $user,
                'user_waivers' => $user_waivers
            ]);
        }
    }

    public function goToDashboard(){
        $waivers = Waiver::where('show_at', 'sign-up')->where('is_active', 1)->get();
        $user = auth()->user();
        $user_waivers = UserWaiver::where('user_id', $user->id)
        ->get();
        if(count($waivers) == count($user_waivers)){
            return redirect(route('ss.member.dashboard', ["subdomain" => request()->session()->get('business_settings')['subdomain']]));
        }
    }

    public function storeWaiver(Request $request){
        $waiverValidation = WaiverValidationAndSaveService::validateIfHasWaiver();

        if(!empty($waiverValidation)) {
            return response()->json($waiverValidation,422);
        }else{
            $waiver = UserWaiver::create([
                'user_id' => auth()->user()->id,
                'waiver_id' => $request['waiver_id'],
                'family_member_id' => $family_member_id ?? null,
                'user_waiver_accepted_data' => $request['waiver_data']['data'],
                'signature' => $request['waiver_data']['signature'],
            ]);
            return response()->json($waiver);
        }

    }
}
