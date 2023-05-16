<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use Inertia\Inertia;
use App\Traits\GateHelper;
use App\Enums\AppUserSource;
use App\Models\Subscription;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Gate;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        if (Gate::denies('viewAny', [AppUserSource::admin->name, 'dashboard', 'viewAny'])) {
            abort(403);            
        }
        return Inertia::render('Admin/Dashboard', [
            'page_title' => __('Admin dashboard'),
            'header' => __('Admin dashboard'),
            'partners_count' => User::partner()->count(),
            'active_subscriptions_count' => Subscription::active()->count(),
        ]);
    }
}
