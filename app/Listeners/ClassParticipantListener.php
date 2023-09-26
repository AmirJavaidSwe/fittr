<?php

namespace App\Listeners;

use App\Events\ClassParticipant;
use App\Services\Shared\NotificationService;
use Carbon\Carbon;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Arr;

class ClassParticipantListener extends PartnerListener implements ShouldQueue
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
     * @param  \App\Events\ClassParticipant  $event
     * @return void
     */
    public function handle(ClassParticipant $event)
    {
        //parent method will set 1 public prop on this class: array $business_settings
        $this->setPartnerConnection($event);
        $classLesson = $event->classLesson;
        $data = $event->data;
        $business_settings = $this->business_settings;
        $timezone = $this->business_settings['timezone'];
        $dateFormat = $business_settings['date_format']->format_string;
        $timeFormat = $business_settings['time_format']->format_string;

        foreach ($classLesson->bookings as $booking) {
            $params = [
                "SUBJECT" => "Important class message",
                "CLASS_TITLE" => $classLesson->title,
                "CLASS_DATE" => $classLesson->start_date->tz($timezone)->format($dateFormat),
                "CLASS_START" => $classLesson->start_date->tz($timezone)->format($timeFormat),
                "CLASS_END" => $classLesson->end_date->tz($timezone)->format($timeFormat),
                "MEMBER_NAME" => $booking->user->full_name,
                "CLASS_TYPE" => $classLesson->classType?->title,
                "CLASS_STUDIO" => $classLesson->studio?->title,
                "CLASS_LOCATION" => $classLesson->studio?->location?->title,
            ];

            $mailService = new NotificationService;
            $mailService
            ->setTemplate(
                    $data['template_id'],
                    Arr::only($data, ['subject', 'content', 'content_plain'])
            )
            ->setParams($params)
            ->send($booking->user->email);
        }
    }
}
