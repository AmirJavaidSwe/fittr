<?php

namespace App\Http\Controllers\Store;

use Inertia\Inertia;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\Partner\FamilyMemberRequest;
use App\Models\Partner\FamilyMember;

class FamilyMemberController extends Controller
{

    public function index(Request $request)
    {

        return Inertia::render('Store/Member/Family/Index', [
            'family_members' => FamilyMember::where('user_id', auth()->user()->id)->get(),
            'page_title' => __('Family'),
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
        $member = FamilyMember::find($id);

        $member->fill($request->all());

        $member->save();

        if($request->has_image == false) {
            $member->profile_photo_path = null;
        }

        if($request->hasFile('profile_photo')) {
            $member->updateProfilePhoto($request->profile_photo);
        }
        $member->save();

        return $this->redirectBackSuccess(__('Family member updated successfully'));
    }

    public function destroy($subdomain, $id)
    {
        $member = FamilyMember::find($id);
        $member->delete();

        return $this->redirectBackSuccess(__('Family member deleted successfully'));
    }
}
