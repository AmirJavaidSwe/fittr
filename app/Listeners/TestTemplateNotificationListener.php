<?php

namespace App\Listeners;

use App\Events\TestTemplateNotification;
use App\Services\Shared\NotificationService;

class TestTemplateNotificationListener extends PartnerListener
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  \App\Events\TestTemplateNotification  $event
     * @return void
     */
    public function handle(TestTemplateNotification $event)
    {
        //parent method will set 1 public prop on this class: array $business_settings
        $this->setPartnerConnection($event);
        $data = $event->data;
        $business_settings = $this->business_settings;

        foreach ($data['placeholders'] as $placeholder) {
            if ($placeholder == 'ACTION_BUTTON') {
                $buttonHtml = view('emails.partials.button', [
                    'buttonLink' => '#FAKE_'.$placeholder,
                    'buttonText' => 'FAKE_'.$placeholder,
                    'buttonColor' => 'primary',
                ])->render();
                $params[$placeholder] = $buttonHtml;
            } else {
                $params[$placeholder] = "FAKE_" . $placeholder;
            }
        }

        $mailService = new NotificationService;
        $mailService->setTemplate($data)
            ->setParams($params)
            ->send($data['recipient_email'], $business_settings);
    }
}
