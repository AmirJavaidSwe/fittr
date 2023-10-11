<?php

namespace App\Services\Shared;

use App\Models\StripeEvent;
use App\Models\Partner\User;
use App\Models\Partner\Membership;

class StoreMemberProfileService
{
    public function getStoreMemberProfile() {

        $member = User::with('images')->select('id', 'role', 'first_name', 'last_name', 'email');
        $member = $member->whereId(request()->member_id)

        ->first();
        $member =
        return Inertia::render('Store/Member/Profile/Index', [
            'user' => User::with('images')->select('id', 'role', 'first_name', 'last_name', 'email')->whereId(auth()->user()->id)->first(),
            'page_title' => __('Profile management'),
            'header' => __('Profile management'),
        ]);
    }
}