<?php

namespace App\Http\Controllers\Partner;

use App\Enums\PackType;
use App\Enums\StripePriceType;
use App\Http\Controllers\Controller;
use App\Models\Partner\Membership;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PartnerMembershipController extends Controller
{
    public $search;
    public $per_page;
    public $order_by;
    public $order_dir;

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $this->search = $request->query('search', null);
        $this->per_page = $request->query('per_page', 10);
        $this->order_by = $request->query('order_by', 'created_at');
        $this->order_dir = $request->query('order_dir', 'desc');

        return Inertia::render('Partner/Membership/Index', [
            'memberships' => Membership::with([
                    'pack',
                    'pack_price',
                    'order',
                    'order_item',
                    'user',
                ])
                ->orderBy($this->order_by, $this->order_dir)
                ->paginate($this->per_page)
                ->withQueryString(),
            'pack_types' => PackType::labels(),
            'price_types' => StripePriceType::labels(),

            'search' => $this->search,
            'per_page' => intval($this->per_page),
            'order_by' => $this->order_by,
            'order_dir' => $this->order_dir,
            'page_title' => __('Memberships'),
            'header' => array(
                [
                    'title' => __('Memberships'),
                    'link' => route('partner.memberships.index'),
                ]
            ),
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Membership $membership)
    {
        return Inertia::render('Partner/Membership/Show', [
            'page_title' => __('Membership details'),
            'header' => array(
                [
                    'title' => __('Memberships'),
                    'link' => route('partner.memberships.index'),
                ],
                [
                    'title' => '/',
                    'link' => null,
                ],
                [
                    'title' => __('Membership details'),
                    'link' => null,
                ],
            ),
            'membership' => $membership->load([
                'pack',
                'pack_price',
                'order',
                'order_item',
                'user',
            ]),
        ]);
    }

}
