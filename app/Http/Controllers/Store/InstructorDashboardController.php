<?php

namespace App\Http\Controllers\Store;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;

class InstructorDashboardController extends Controller
{
    public function index(Request $request)
    {
        return Inertia::render('Store/Instructor/Dashboard', [
            'page_title' => __('Instructor Dashboard'),
            'header' => __('Instructor Dashboard'),
        ]);
    }
}
