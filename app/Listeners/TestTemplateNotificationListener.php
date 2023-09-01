<?php

namespace App\Listeners;

use App\Events\TestTemplateNotification;
use App\Services\Shared\NotificationService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Mail;

class TestTemplateNotificationListener extends PartnerListener implements ShouldQueue
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

        $params = [
            "SUBJECT" => "FAKE_SUBJECT",
            "CLASS_TITLE" => "FAKE_CLASS_TITLE",
            "CLASS_DATE" => "FAKE_CLASS_DATE",
            "CLASS_START" => "FAKE_CLASS_START",
            "CLASS_END" => "FAKE_CLASS_END",
            "MEMBER_NAME" => "FAKE_MEMBER_NAME",
            "CLASS_TYPE" => "FAKE_CLASS_TYPE",
            "CLASS_STUDIO" => "FAKE_CLASS_STUDIO",
            "CLASS_LOCATION" => "FAKE_CLASS_LOCATION",
        ];

        $mailService = new NotificationService;
        $mailService->setTemplate($data)
            ->setParams($params)
            ->send($data['recipient_email']);
    }
}
