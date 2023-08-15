<?php

namespace App\Http\Controllers\Admin;

use Inertia\Inertia;
use App\Models\StripeEvent;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
// use App\Services\Shared\StripeEventService;

class StripeEventController extends Controller
{
    public $search;
    public $per_page;
    public $order_by;
    public $order_dir;

    public function __construct()
    {
        // $this->service = $service;
    }

    public function index(Request $request)
    {
        $this->search = $request->query('search', null);
        $this->per_page = $request->query('per_page', 10);
        $this->order_by = $request->query('order_by', 'id');
        $this->order_dir = $request->query('order_dir', 'desc');

        return Inertia::render('Admin/StripeEvents/Index', [
            'stripe_events' => StripeEvent::with(['business.name'])
                ->orderBy($this->order_by, $this->order_dir)
                ->when($this->search, function ($query) {
                    $query->where(function ($query) {
                        $query
                            ->orWhere('id', $this->search)
                            ->orWhere('type', 'LIKE', '%' . $this->search . '%')
                            ->orWhere('stripe_id', 'LIKE', '%' . $this->search . '%')
                            ->orWhere('connected_account', 'LIKE', '%' . $this->search . '%');
                    });
                })
                ->paginate($this->per_page)
                ->withQueryString(),
            'search' => $this->search,
            'per_page' => intval($this->per_page),
            'order_by' => $this->order_by,
            'order_dir' => $this->order_dir,
            'page_title' => __('Stripe Events'),
            'header' => __('Stripe Events'),
        ]);
    }

    public function show(Request $request, $id)
    {
        $se = StripeEvent::with(['business.name'])->findOrFail($id);

        return Inertia::render('Admin/StripeEvents/Show', [
            'se' => $se,
            'page_title' => __('Stripe Event'),
            'header' => array(
                [
                    'title' => __('Stripe Events'),
                    'link' => route('admin.se.index'),
                ],
                [
                    'title' => '/',
                    'link' => null,
                ],
                [
                    'title' => $se->stripe_id,
                    'link' => route('admin.se.show', ['id' => $se->id]),
                ],
            ),
        ]);
    }
}
