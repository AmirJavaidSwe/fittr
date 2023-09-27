<?php

namespace App\Services\Shared;

use App\Enums\NotificationMailDriver;
use App\Models\Partner\NotificationTemplate;
use Exception;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\View;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Str;
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

        $this->template['content_plain'] = strip_tags($this->template['content']);
        $this->template['content_plain'] = html_entity_decode(preg_replace("/[\r\n]{2,}/", "\n\n", $this->template['content_plain']), ENT_QUOTES, 'UTF-8');

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

        return $this;
    }

    public function preview()
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

        return View::make('emails.html_template', $this->template);
    }
}