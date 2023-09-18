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
        $waivers = Waiver::where('show_at', 'family-add')->where('is_active', 1)->get();
        $user_waivers = UserWaiver::where('user_id', auth()->user()->id)->get();
        if($waivers) {
            $count = 0 ;
            foreach($waivers as $waiver){
                $user_waiver = UserWaiver::where('user_id', auth()->user()->id)
                ->where('waiver_id', $waiver->id)
                ->pluck('family_member_id')->toArray();
                $count++;
            }
            if($count == count($waivers)){
                return Inertia::render('Store/Member/Family/Index', [
                    'family_members' => FamilyMember::where('user_id', auth()->user()->id)->get(),
                    'page_title' => __('Family'),
                    'waivers' => $waivers,
                    'user_waivers' => $user_waivers ?? null
                ]);
            }
        }
        return Inertia::render('Store/Member/Family/Index', [
            'family_members' => FamilyMember::where('user_id', auth()->user()->id)->get(),
            'page_title' => __('Family'),
            'waivers' => $waivers,
            'user_waivers' => $user_waivers ?? null,
            'user' => auth()->user(),
        ]);
    }

    public function store(FamilyMemberRequest $request)
    {
        $member = FamilyMember::create($request->all());
        foreach($request['waiver_data'] as $waiver){
            $waiver = UserWaiver::where([['id',$waiver['id']],['waiver_id',$waiver['waiver_id']],['user_id',auth()->user()->id]])->first();
            $waiver->family_member_id = $member->id;
            $waiver->save();
        }
        if ($request->hasFile('profile_photo')) {
            $member->updateProfilePhoto($request->profile_photo);
        }
        $member->user_id = auth()->user()->id;
        $member->save();

        return $this->redirectBackSuccessWithSubdomain(__('Family member added successfully'), 'ss.member.family.index');
        // }else{
        //     $waivers = Waiver::where('show_at', 'family-add')->where('is_active', 1)->get();
        //     $user_waivers = UserWaiver::where('user_id', auth()->user()->id)->get();
        //     return Inertia::render('Store/Member/Family/Index', [
        //         'family_members' => FamilyMember::where('user_id', auth()->user()->id)->get(),
        //         'page_title' => __('Family'),
        //         'waivers' => $waivers,
        //         'user_waivers' => $user_waivers ?? null,
        //         'user' => auth()->user(),
        //     ]);
        // }
    }

    public function update(FamilyMemberRequest $request, $subdomain, $id)
    {

        $member = FamilyMember::findOrFail($id);
        foreach($request['waiver_data'] as $waiver){
            $waiver = UserWaiver::where([['id',$waiver['id']],['waiver_id',$waiver['waiver_id']],['user_id',auth()->user()->id]])->first();
            $waiver->family_member_id = $member->id;
            $waiver->save();
        }
        $member->update($request->validated());

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
