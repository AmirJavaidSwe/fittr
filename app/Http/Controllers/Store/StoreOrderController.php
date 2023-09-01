<?php

namespace App\Http\Controllers\Store;

use App\Http\Controllers\Controller;
use App\Models\Partner\Order;
use Illuminate\Http\Request;
use Inertia\Inertia;

class StoreOrderController extends Controller
{
    public $search;
    public $per_page;
    public $order_by;
    public $order_dir;

    public function index(Request $request, $subdomain)
    {
        $this->search = $request->query('search', null);
        $this->per_page = $request->query('per_page', 10);
        $this->order_by = $request->query('order_by', 'created_at');
        $this->order_dir = $request->query('order_dir', 'desc');
        $user = auth()->user();
        
        return Inertia::render('Store/Member/Orders/Index', [
            'orders' => Order::with(['items.pack_price.priceable'])
                ->orderBy($this->order_by, $this->order_dir)
                ->where('user_id', $user->id)
                ->paginate($this->per_page)
                ->withQueryString(),
            'search' => $this->search,
            'per_page' => intval($this->per_page),
            'order_by' => $this->order_by,
            'order_dir' => $this->order_dir,
            'page_title' => __('Orders'),
            'header' => __('Orders'),
        ]);
    }
}
