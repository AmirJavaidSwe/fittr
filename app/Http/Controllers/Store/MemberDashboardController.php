<?php

namespace App\Http\Controllers\Store;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;

class MemberDashboardController extends Controller
{
    public function index(Request $request)
    {
        return Inertia::render('Store/Member/Dashboard', [
            'page_title' => __('Dashboard'),
            'header' => __('Dashboard'),
        ]);
    }
}
