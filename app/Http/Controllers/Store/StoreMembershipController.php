<?php

namespace App\Http\Controllers\Store;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;

class StoreMembershipController extends Controller
{
    public function index(Request $request)
    {
        return Inertia::render('Store/Memberships', [
            'page_title' => __('Memberships'),
            'header' => __('Memberships'),
            'memberships' => [],
        ]);
    }
}
