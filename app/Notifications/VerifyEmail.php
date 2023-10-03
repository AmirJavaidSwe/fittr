<?php

namespace App\Notifications;

use App\Models\Partner\NotificationTemplate;
use App\Services\Shared\NotificationService;
use App\Traits\GenericHelper;
use Illuminate\Auth\Notifications\VerifyEmail as NotificationsVerifyEmail;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\URL;

class VerifyEmail extends NotificationsVerifyEmail
{

    use GenericHelper;

    /**
     * Build the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        $verificationUrl = $this->verificationUrl($notifiable);

        if (static::$toMailCallback) {
            return call_user_func(static::$toMailCallback, $notifiable, $verificationUrl);
        }

        if (! $this->isMainDomain()) {
            $template = NotificationTemplate::active()->where('key', 'EMAIL_VERIFICATION')->first()?->toArray();

            return (new NotificationService)
                ->setTemplate($template)
                ->setParams([
                    'SUBJECT' => 'Verify Email Address',
                    'ACTION_BUTTON' => view('emails.partials.button', [
                        'buttonText' => 'Verify Email Address',
                        'buttonLink' => $verificationUrl,
                        'buttonColor' => 'primary',
                    ])
                ])
                ->buildMailMessage();
        } else {
            return $this->buildMailMessage($verificationUrl);
        }
    }

    /**
     * Get the verify email notification mail message for the given URL.
     *
     * @param  string  $url
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    protected function buildMailMessage($url)
    {
        return (new MailMessage)
            ->subject(Lang::get('Verify Email Address'))
            ->line(Lang::get('Please click the button below to verify your email address.'))
            ->action(Lang::get('Verify Email Address'), $url)
            ->line(Lang::get('If you did not create an account, no further action is required.'));
    }

    /**
     * Get the verification URL for the given notifiable.
     *
     * @param  mixed  $notifiable
     * @return string
     */
    protected function verificationUrl($notifiable)
    {
        if (static::$createUrlCallback) {
            return call_user_func(static::$createUrlCallback, $notifiable);
        }

        $verifyUrl = URL::temporarySignedRoute(
            'verification.verify',
            Carbon::now()->addMinutes(Config::get('auth.verification.expire', 60)),
            [
                'id' => $notifiable->getKey(),
                'hash' => sha1($notifiable->getEmailForVerification()),
            ],
            false
        );

        $subdomain = session('business_settings.subdomain');
        $baseLink = '';

        if ($this->isMainDomain()) {
            $baseLink = route('root');
        } else {
            $baseLink = route('ss.home', ['subdomain' => $subdomain]);
        }

        $verifyUrl = $baseLink . $verifyUrl;

        return $verifyUrl;
    }
}
