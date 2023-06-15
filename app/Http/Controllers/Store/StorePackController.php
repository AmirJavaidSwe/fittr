<?php

namespace App\Http\Controllers\Store;

use App\Http\Controllers\Controller;
use App\Models\Partner\Pack;
use Illuminate\Http\Request;
use Inertia\Inertia;

class StorePackController extends Controller
{
    public function index(Request $request)
    {
        return Inertia::render('Store/Packs', [
            'page_title' => __('Memberships'),
            'header' => __('Memberships'),
            'packs' => Pack::with(['prices'])->get(),
        ]);
    }
}
