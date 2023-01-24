<?php

namespace App\Http\Controllers\Partner;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $partner = $request->user();

        return Inertia::render('Partner/Dashboard', [
            'page_title' => __('My dashboard'),
            'header' => __('My dashboard'),
            'partner' => $partner,
        ]);
    }
}
