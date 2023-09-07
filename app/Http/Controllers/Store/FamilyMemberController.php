<?php

namespace App\Http\Controllers\Store;

use Inertia\Inertia;
use Illuminate\Http\Request;
use App\Models\Partner\Waiver;
use App\Models\Partner\UserWaiver;
use App\Http\Controllers\Controller;
use App\Models\Partner\FamilyMember;
use Illuminate\Support\Facades\Validator;
use App\Services\Partner\WaiverValidationAndSaveService;
use App\Http\Requests\Partner\FamilyMemberRequest;

class FamilyMemberController extends Controller
{

    public function index(Request $request)
    {
        $waiver = Waiver::where('show_at', 'family-add')->where('is_active', 1)->first();
        if($waiver) {
            $user_waiver = UserWaiver::where('user_id', auth()->user()->id)
            ->where('waiver_id', $waiver->id)
            ->pluck('family_member_id')->toArray();
        }
        return Inertia::render('Store/Member/Family/Index', [
            'family_members' => FamilyMember::where('user_id', auth()->user()->id)->get(),
            'page_title' => __('Family'),
            'waiver' => $waiver,
            'user_waiver' => $user_waiver ?? null
        ]);
    }

    public function store(FamilyMemberRequest $request)
    {
        $waiverValidation = WaiverValidationAndSaveService::validateIfHasWaiver();

        if(!empty($waiverValidation)) {
            return back()->withErrors($waiverValidation)->withInput();
        }

        $member = FamilyMember::create($request->all());

        WaiverValidationAndSaveService::saveWaiverAcceptanceData($member->id);

        if ($request->hasFile('profile_photo')) {
            $member->updateProfilePhoto($request->profile_photo);
        }
        $member->user_id = auth()->user()->id;
        $member->save();

        return $this->redirectBackSuccessWithSubdomain(__('Family member added successfully'), 'ss.member.family.index');
    }

    public function update(FamilyMemberRequest $request, $subdomain, $id)
    {

        $waiverValidation = WaiverValidationAndSaveService::validateIfHasWaiver();

        if(!empty($waiverValidation)) {
            return back()->withErrors($waiverValidation)->withInput();
        }

        $member = FamilyMember::findOrFail($id);
        $member->update($request->validated());

        if(!(UserWaiver::where('family_member_id', $member->id)->where('user_id', auth()->user()->id)->exists())) {
            WaiverValidationAndSaveService::saveWaiverAcceptanceData($member->id);
        }

        if ($request->has_image == false) {
            $member->deleteProfilePhoto();
        }

        if ($request->hasFile('profile_photo')) {
            $member->updateProfilePhoto($request->profile_photo);
        };

        return $this->redirectBackSuccessWithSubdomain(__('Family member updated successfully'), 'ss.member.family.index');
    }

    public function destroy($subdomain, $id)
    {
        $member = FamilyMember::findOrFail($id);
        $member->deleteProfilePhoto();
        $member->delete();

        return $this->redirectBackSuccessWithSubdomain(__('Family member deleted successfully'), 'ss.member.family.index');

    }
}
