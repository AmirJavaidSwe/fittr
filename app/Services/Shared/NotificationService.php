<?php

namespace App\Services\Shared;

use App\Enums\NotificationMailDriver;
use App\Models\Partner\NotificationTemplate;
use Exception;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\View;
use Illuminate\Support\HtmlString;
use Soundasleep\Html2Text;
use TijsVerkoyen\CssToInlineStyles\CssToInlineStyles;

class NotificationService
{
    protected array $template;
    protected array $params;
    protected array $business_settings;

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

    public function send($email, $business_settings): void
    {
        if(!@$this->template['status'])
            return;

        if(!@$this->template['mail_driver'])
            $this->template['mail_driver'] = NotificationMailDriver::get('smtp');

        $this->business_settings = $business_settings;

        $this->replaceParams()
            ->applyTheme();


        $this->template['to_email'] = collect(Arr::wrap(explode(',', $email)))
            ->map(fn($item) => trim($item));

        switch($this->template['mail_driver']) {
            case NotificationMailDriver::get('sendgrid'):
                //
                break;
            case NotificationMailDriver::get('aws_ses'):
                //
                break;
            default: // smtp
                foreach ($this->template['to_email'] as $to_email) {
                    Mail::send(['emails.html_template', 'emails.text_template'], $this->template, function ($message) use ($to_email) {
                        $message->to($to_email)
                            ->from($this->template['from_email'], $this->template['from_name'])
                            ->subject($this->template['subject']);
                    });
                }
                break;
        }
    }

    protected function replaceParams(): self
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

        return $this;
    }

    protected function applyTheme(): self
    {
        $this->template['content'] = View::make('emails.layout', [
            'business_settings' => $this->business_settings,
            'template' => $this->template,
        ])->render();

        $this->template['content'] = new HtmlString((new CssToInlineStyles)->convert(
            $this->template['content'], View::make('emails.theme.default', [])->render()
        ));

        $this->template['content_plain'] = Html2Text::convert($this->template['content']);

        return $this;
    }

    public function preview(): array
    {

        $this->business_settings = session('business_settings');

        $placeholders = $this->template['placeholders'] ?? [];
        $params = [];

        foreach ($placeholders as $placeholder) {
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

        $this->setParams($params)
            ->replaceParams()
            ->applyTheme();

        return [
            'subject' => $this->template['subject'],
            'content' => View::make('emails.html_template', $this->template)->render(),
        ];
    }

    public function buildMailMessage(): MailMessage
    {
        $this->business_settings = session('business_settings');

        $this->replaceParams()
            ->applyTheme();

        return (new MailMessage)
            ->view(['emails.html_template', 'emails.text_template'], $this->template)
            ->from($this->template['from_email'], $this->template['from_name'])
            ->subject($this->template['subject']);
    }
}