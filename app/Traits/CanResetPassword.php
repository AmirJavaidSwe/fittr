<?php

namespace App\Traits;

use App\Notifications\ResetPassword as ResetPasswordNotification;

trait CanResetPassword
{
    /**
     * Send the password reset notification.
     *
     * @param  string  $token
     * @return void
     */
    public function sendPasswordResetNotification($token)
    {
        $this->notify(new ResetPasswordNotification($token));
    }
}
