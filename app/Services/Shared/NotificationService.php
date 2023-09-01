<?php

namespace App\Services\Shared;

use App\Enums\NotificationMailDriver;
use App\Models\Partner\NotificationTemplate;
use Exception;
use Illuminate\Support\Facades\Mail;

class NotificationService
{
    protected array $template;
    protected array $params;

    public function __construct()
    {
        //
    }

    /**
     * Set the array data or id of NotificationTemplate model.
     *
     * @param array|int $data array or id of NotificationTemplate model
     * @param array $contents overrides the template data such as array keys content | content_plain
     **/
    public function setTemplate(array|int $data, array $contents = []): self
    {
        if (is_int($data))
            $data = NotificationTemplate::find($data)->toArray();

        foreach ($contents as $key => $value)
            $data[$key] = $value;

        $this->template = $data;

        return $this;
    }

    /**
     * Set the parameters to be replaced by placeholders used in the content or plain content.
     *
     * @param array $params array of respective key and value pair of placeholders used.
     **/
    public function setParams(array $params): self
    {

        $this->params = $params;

        return $this;
    }

    public function send($email): void
    {
        if(!@$this->template['status'])
            return;

        if(!@$this->template['mail_driver'])
            $this->template['mail_driver'] = NotificationMailDriver::get('smtp');

        $this->replaceParams();

        $this->template['to_email'] = $email;

        switch($this->template['mail_driver']) {
            case NotificationMailDriver::get('sendgrid'):
                //
                break;
            case NotificationMailDriver::get('aws_ses'):
                //
                break;
            default: // smtp
                Mail::send(['emails.html_template', 'emails.text_template'], $this->template, function ($message) {
                    $message->to($this->template['to_email'])
                        ->from($this->template['from_email'], $this->template['from_name'])
                        ->subject($this->template['subject']);
                });
                break;
        }
    }

    protected function replaceParams(): void
    {
        if(!@$this->template['placeholders'] || !is_array($this->template['placeholders']))
            throw new Exception("The template must contain valid 'placeholders' data.");

        foreach ($this->template['placeholders'] as $placeholder) {

            if (!@$this->params[$placeholder])
                throw new Exception("'$placeholder' data not found in the parameters.");

            $this->template['content'] = str_replace(
                '[#'.$placeholder.'#]',
                $this->params[$placeholder],
                $this->template['content']
            );

            $this->template['content_plain'] = str_replace(
                '[#'.$placeholder.'#]',
                $this->params[$placeholder],
                $this->template['content_plain']
            );

            $this->template['subject'] = str_replace(
                '[#'.$placeholder.'#]',
                $this->params[$placeholder],
                $this->template['subject']
            );
        }
    }
}