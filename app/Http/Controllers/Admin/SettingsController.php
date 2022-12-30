<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;

class SettingsController extends Controller
{
    public function index(Request $request)
    {
        return Inertia::render('Admin/Settings', [
            'page_title' => __('Settings'),
            'header' => __('Settings'),
        ]);
    }
}
