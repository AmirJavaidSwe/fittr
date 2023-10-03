<?php

namespace App\Notifications;

use App\Models\Partner\NotificationTemplate;
use App\Services\Shared\NotificationService;
use App\Traits\GenericHelper;
use Illuminate\Auth\Notifications\ResetPassword as NotificationsResetPassword;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\Lang;

class ResetPassword extends NotificationsResetPassword
{
    use GenericHelper;
    /**
     * Create a notification instance.
     *
     * @param  string  $token
     * @return void
     */
    /* public function __construct($token)
    {
        parent::__construct($token);
    } */

    /**
     * Build the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        if (static::$toMailCallback) {
            return call_user_func(static::$toMailCallback, $notifiable, $this->token);
        }

        $resetUrl = $this->resetUrl($notifiable);

        if (! $this->isMainDomain()) {
            $template = NotificationTemplate::active()->where('key', 'RESET_PASSWORD')->first()?->toArray();

            return (new NotificationService)
                ->setTemplate($template)
                ->setParams([
                    'SUBJECT' => 'Reset Password Notification',
                    'EXPIRY_MINUTES' => config('auth.passwords.'.config('auth.defaults.passwords').'.expire'),
                    'ACTION_BUTTON' => view('emails.partials.button', [
                        'buttonText' => 'Reset Password',
                        'buttonLink' => $resetUrl,
                        'buttonColor' => 'primary',
                    ])
                ])
                ->buildMailMessage();
        } else {
            return $this->buildMailMessage($resetUrl);
        }

        // return $this->buildMailMessage($this->resetUrl($notifiable));
    }

    /**
     * Get the reset password notification mail message for the given URL.
     *
     * @param  string  $url
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    protected function buildMailMessage($url)
    {
        return (new MailMessage)
            ->subject(Lang::get('Reset Password Notification'))
            ->line(Lang::get('You are receiving this email because we received a password reset request for your account.'))
            ->action(Lang::get('Reset Password'), $url)
            ->line(Lang::get('This password reset link will expire in :count minutes.', ['count' => config('auth.passwords.'.config('auth.defaults.passwords').'.expire')]))
            ->line(Lang::get('If you did not request a password reset, no further action is required.'));
    }

    /**
     * Get the reset URL for the given notifiable.
     *
     * @param  mixed  $notifiable
     * @return string
     */
    protected function resetUrl($notifiable)
    {
        if (static::$createUrlCallback) {
            return call_user_func(static::$createUrlCallback, $notifiable, $this->token);
        }

        return url(route('password.reset', [
            'token' => $this->token,
            'email' => $notifiable->getEmailForPasswordReset(),
        ], false));
    }
}
