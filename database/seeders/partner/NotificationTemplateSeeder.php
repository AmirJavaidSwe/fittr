<?php

namespace Database\Seeders\Partner;

use DB;
use Storage;
use Illuminate\Database\Seeder;

class NotificationTemplateSeeder extends Seeder
{
    /**
     * Run the database seeds. php artisan db:seed --class=Database\Seeders\Partner\NotificationTemplateSeeder
     *
     * @return void
     */
    public function run()
    {
        if (!Storage::disk('seeders')->exists('/partner/data/notification_templates.json')) {
            dump('/partner/data/notificationTemplates.json file does not exist!');
            return;
        }
        $notificationTemplates = json_decode(Storage::disk('seeders')->get('/partner/data/notification_templates.json'));

        foreach ($notificationTemplates as $notificationTemplate) {
            DB::table('notification_templates')->insert([
                'title' => $notificationTemplate->title,
                'key' => $notificationTemplate->key,
                'placeholders' => json_encode($notificationTemplate->placeholders),
                'from_name' => $notificationTemplate->from_name, // Need to discuss the flow to put business data here.
                'from_email' => $notificationTemplate->from_email, // Need to discuss the flow to put business data here.
                'subject' => $notificationTemplate->subject,
                'content' => $notificationTemplate->content,
                'content_plain' => $notificationTemplate->content_plain,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
