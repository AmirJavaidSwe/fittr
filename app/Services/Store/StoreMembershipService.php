<?php

namespace App\Services\Store;

class StoreMembershipService
{

    public function canCancel($membership): bool
    {
        //no term or 1 (term served, as at least 1 charge has been made)
        if(empty($membership->min_term) && $membership->min_term == 1 ){
            return true;
        }

        $membership->load(['membership_charges']);
        $charges_made = $membership->membership_charges;

        return $membership->min_term <= $charges_made->count();
    }

}