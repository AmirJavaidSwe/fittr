<?php

namespace App\Http\Controllers\Partner;

use App\Events\TestTemplateNotification;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Http\Request;
use App\Models\Partner\NotificationTemplate;
use App\Http\Controllers\Controller;
use App\Http\Requests\Partner\NotificationTemplateFormRequest;
use App\Rules\CommaSeparatedEmails;
use App\Services\Shared\NotificationService;

class PartnerNotificationTemplateController extends Controller
{
    protected $search;
    protected $per_page;
    protected $order_by;
    protected $order_dir;

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index(Request $request)
    {
        $this->search = $request->query('search', null);
        $this->per_page = $request->query('per_page', 10);
        $this->order_by = $request->query('order_by', 'id');
        $this->order_dir = $request->query('order_dir', 'asc');

        return Inertia::render('Partner/NotificationTemplate/Index', [
            'notificationTemplates' => NotificationTemplate::orderBy($this->order_by, $this->order_dir)
                ->when($this->search, function ($query) {
                    $query->where(function($query) {
                        $query->orWhere('id', intval($this->search))
                        ->orWhere('title', 'LIKE', '%'.$this->search.'%');
                    });
                })
                ->paginate($this->per_page)
                ->withQueryString(),
            'search' => $this->search,
            'per_page' => intval($this->per_page),
            'order_by' => $this->order_by,
            'order_dir' => $this->order_dir,
            'page_title' => __('Settings - Notification Templates'),
            'header' => array(
                [
                    'title' => __('Settings'),
                    'link' => route('partner.settings.index'),
                ],
                [
                    'title' => '/',
                    'link' => null,
                ],
                [
                    'title' => __('Notification Templates'),
                    'link' => null,
                ],
            ),
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Partner\NotificationTemplate  $notificationTemplate
     * @return Response
     */
    public function edit(NotificationTemplate $notificationTemplate)
    {
        return Inertia::render('Partner/NotificationTemplate/Edit', [
            'page_title' => __('Edit Notification Template'),
            'header' => array(
                [
                    'title' => __('Settings'),
                    'link' => route('partner.settings.index'),
                ],
                [
                    'title' => '/',
                    'link' => null,
                ],
                [
                    'title' => __('Notification Templates'),
                    'link' => route('partner.notification-templates.index'),
                ],
                [
                    'title' => '/',
                    'link' => null,
                ],
                [
                    'title' => __('Edit Notification Template'),
                    'link' => null,
                ],
            ),
            'notificationTemplate' => $notificationTemplate,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\Partner\NotificationTemplateFormRequest  $request
     * @param  \App\Models\Partner\NotificationTemplate  $notificationTemplate
     * @return \Illuminate\Http\Response
     */
    public function update(NotificationTemplateFormRequest $request, NotificationTemplate $notificationTemplate)
    {
        $validated = $request->validated();

        $notificationTemplate->update($validated);

        return $this->redirectBackSuccess(__('Notification template updated successfully'), 'partner.notification-templates.index');
    }

    public function preview(Request $request)
    {
        return (new NotificationService())
            ->setTemplate($request->template_id ?? $request->all())
            ->preview();
    }

    public function test(Request $request)
    {
        $request->validate([
            'recipient_email' => ['required', new CommaSeparatedEmails],
        ],
        [],
        [
            'recipient_email' => 'recipient email'
        ]);

        event(new TestTemplateNotification($request->all()));
        return $this->redirectBackSuccess('The notification has been sent.');
    }

    public function load(Request $request, NotificationTemplate $notificationTemplate)
    {
        $notificationTemplate->content = view('emails.html_template', ['content' => $notificationTemplate->content])->render();
        return response()->json(['template' => $notificationTemplate]);
    }
}
