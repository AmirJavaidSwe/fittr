<?php

namespace App\Http\Controllers\Store;

use Inertia\Inertia;
use App\Enums\BookingStatus;
use Illuminate\Http\Request;
use App\Models\Partner\Booking;
use App\Events\BookingCancellation;
use App\Events\BookingConfirmation;
use App\Models\Partner\ClassLesson;
use App\Http\Controllers\Controller;
use App\Http\Requests\Partner\MemberFamilyRequest;
use App\Models\Partner\MemberFamily;

class MemberFamilyController extends Controller
{

    public function index(Request $request)
    {

        return Inertia::render('Store/Member/Family/Index', [
            'family_members' => MemberFamily::where('user_id', auth()->user()->id)->get(),
            'page_title' => __('Family'),
        ]);
    }

    public function store(MemberFamilyRequest $request)
    {
        $member = MemberFamily::create($request->all());

        if($request->hasFile('profile_photo')) {
            $member->updateProfilePhoto($request->profile_photo);
        }
        $member->user_id = auth()->user()->id;
        $member->save();

        return $this->redirectBackSuccess(__('Family member added successfully'));
    }
    public function update(MemberFamilyRequest $request, $subdomain, $id)
    {
        $member = MemberFamily::find($id);

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
        $member = MemberFamily::find($id);
        $member->delete();

        return $this->redirectBackSuccess(__('Family member deleted successfully'));
    }
}
