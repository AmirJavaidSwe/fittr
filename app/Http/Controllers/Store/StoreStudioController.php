<?php

namespace App\Http\Controllers\Store;

use App\Http\Controllers\Controller;
use App\Models\Partner\Studio;
use Illuminate\Http\Request;
use Inertia\Inertia;

class StoreStudioController extends Controller
{
    public function index(Request $request)
    {
        return Inertia::render('Store/Studios', [
            'page_title' => __('Studios'),
            'header' => __('Studios'),
            'studios' => Studio::with(['amenities'])->get(),
        ]);
    }
}
