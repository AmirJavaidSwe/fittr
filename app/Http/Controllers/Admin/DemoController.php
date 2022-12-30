<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DemoController extends Controller
{
    public function index(Request $request)
    {
        return Inertia::render('DemoPage', [
            'page_title' => __('Demo page'),
            'header' => __('Demo page'),
        ]);
    }
}
