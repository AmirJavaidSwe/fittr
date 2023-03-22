<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Package;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;

class SettingsController extends Controller
{
    public function index()
    {
        return Inertia::render('Admin/Settings', [
            'page_title' => __('Settings'),
            'header' => __('Settings'),
            'packages' => Package::all(), //tab
            'admins' => User::admin()->select('id','name','email','profile_photo_path','created_at')->get(), //tab
        ]);
    }
}
