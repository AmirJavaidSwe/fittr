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
        $user_waivers = [];
        $waivers = Waiver::where('show_at', 'sign-up')->where('is_active', 1)->get();
        if($waivers->isNotEmpty()) {
            $waiver_ids = $waivers->pluck('id')->toArray();
            $user_waivers = UserWaiver::where('user_id', auth()->user()->id)->whereIn('waiver_id', $waiver_ids)->pluck('id')->toArray();
            return Inertia::render('Store/WaiverVerification', [
                'page_title' => __('Waiver Verification'),
                'waivers' => $waivers,
                'user_waivers' => $user_waivers
            ]);
        }
        return redirect(route('ss.member.dashboard', ["subdomain" => request()->session()->get('business_settings')['subdomain']]));

    }
    public function store(Request $request)
    {
        $waiverValidation = WaiverValidationAndSaveService::validateIfHasWaiver();

        if(!empty($waiverValidation)) {
            return back()->withErrors($waiverValidation)->withInput();
        }
        $id = WaiverValidationAndSaveService::saveWaiverAcceptanceData();
        $extra = array('user_waiver_id' => $id);
        return $this->redirectBackSuccess(__('Saved'))->with('extra', $extra);

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

    public function storeFamilyWaiver(Request $request){
        $waiverValidation = WaiverValidationAndSaveService::validateIfHasWaiver();
        if(!empty($waiverValidation)) {
            return back()->withErrors($waiverValidation)->withInput();
        }
        $id = WaiverValidationAndSaveService::saveWaiverAcceptanceData($request->family_member_id);

        $extra = array('user_waiver_id' => $id);

        return $this->redirectBackSuccess(__('Saved'))->with('extra', $extra);
    }
    public function storeBookingWaiver(Request $request){

        $waiverValidation = WaiverValidationAndSaveService::validateIfHasWaiver();
        if(!empty($waiverValidation)) {
            return back()->withErrors($waiverValidation)->withInput();
        }
        $id = WaiverValidationAndSaveService::saveWaiverAcceptanceData($request->family_member_id);

        $extra = array('user_waiver_id' => $id);

        return $this->redirectBackSuccess(__('Saved'))->with('extra', $extra);
    }
}
