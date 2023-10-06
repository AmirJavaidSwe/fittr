<?php

namespace App\Traits;

use App\Notifications\VerifyEmail;

trait MustVerifyEmail
{
    /**
     * Send the email verification notification.
     *
     * @return void
     */
    public function sendEmailVerificationNotification()
    {
        $this->notify(new VerifyEmail);
    }
}
