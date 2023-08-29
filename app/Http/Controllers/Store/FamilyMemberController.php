<?php

namespace App\Http\Controllers\Store;

use Inertia\Inertia;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\Partner\FamilyMemberRequest;
use App\Models\Partner\FamilyMember;
use App\Models\Partner\Waiver;

class FamilyMemberController extends Controller
{

    public function index(Request $request)
    {
        return Inertia::render('Store/Member/Family/Index', [
            'family_members' => FamilyMember::where('user_id', auth()->user()->id)->get(),
            'page_title' => __('Family'),
            'waiver' => Waiver::where('show_at', 'family-add')->whereNull('deleted_at')->first(),
        ]);
    }

    public function store(FamilyMemberRequest $request)
    {
        $member = FamilyMember::create($request->all());

        if($request->hasFile('profile_photo')) {
            $member->updateProfilePhoto($request->profile_photo);
        }
        $member->user_id = auth()->user()->id;
        $member->save();

        return $this->redirectBackSuccess(__('Family member added successfully'));
    }

    public function update(FamilyMemberRequest $request, $subdomain, $id)
    {
        $member = FamilyMember::findOrFail($id);
        $member->update($request->validated());

        if($request->has_image == false) {
            $member->deleteProfilePhoto();
        }

        if($request->hasFile('profile_photo')) {
            $member->updateProfilePhoto($request->profile_photo);
        };

        return $this->redirectBackSuccess(__('Family member updated successfully'));
    }

    public function destroy($subdomain, $id)
    {
        $member = FamilyMember::findOrFail($id);
        $member->deleteProfilePhoto();
        $member->delete();

        return $this->redirectBackSuccess(__('Family member deleted successfully'));
    }
}
