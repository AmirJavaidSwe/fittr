<?php

namespace App\Http\Controllers\Partner;

use App\Http\Controllers\Controller;
use App\Models\Partner\Order;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PartnerOrderController extends Controller
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

        return Inertia::render('Partner/Order/Index', [
            'orders' => Order::with([
                    // eager loading multiple nested relationships:
                    'items' => [
                        'pack_price.priceable',
                        'membership',
                    ],
                    'user',
                ])
                ->orderBy($this->order_by, $this->order_dir)
                ->paginate($this->per_page)
                ->withQueryString(),

            'search' => $this->search,
            'per_page' => intval($this->per_page),
            'order_by' => $this->order_by,
            'order_dir' => $this->order_dir,
            'page_title' => __('Orders'),
            'header' => array(
                [
                    'title' => __('Orders'),
                    'link' => route('partner.orders.index'),
                ]
            ),
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Order $order)
    {
        return Inertia::render('Partner/Order/Show', [
            'page_title' => __('Order details'),
            'header' => array(
                [
                    'title' => __('Orders'),
                    'link' => route('partner.orders.index'),
                ],
                [
                    'title' => '/',
                    'link' => null,
                ],
                [
                    'title' => __('Order details'),
                    'link' => null,
                ],
            ),
            'order' => $order->load([
                'items' => [
                    'pack_price.priceable',
                    'membership',
                ],
                'user',
            ]),
        ]);
    }

}
