<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Subscription;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        return Inertia::render('Admin/Dashboard', [
            'page_title' => __('Admin dashboard'),
            'header' => __('Admin dashboard'),
            'partners_count' => User::partner()->count(),
            'active_subscriptions_count' => Subscription::active()->count(),
        ]);
    }
}
