<?php

namespace App\Http\Controllers\Store;

use App\Http\Controllers\Controller;
use App\Models\Partner\Location;
use Illuminate\Http\Request;
use Inertia\Inertia;

class StoreLocationController extends Controller
{
    public function index(Request $request)
    {
        return Inertia::render('Store/Locations', [
            'page_title' => __('Locations'),
            'header' => __('Locations'),
            'locations' => Location::active()->with(['amenities', 'images', 'studios'])->get(),
        ]);
    }
}
