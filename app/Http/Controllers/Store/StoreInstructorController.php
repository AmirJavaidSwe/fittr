<?php

namespace App\Http\Controllers\Store;

use App\Http\Controllers\Controller;
use App\Models\Partner\User;
use Illuminate\Http\Request;
use Inertia\Inertia;

class StoreInstructorController extends Controller
{
    public function index(Request $request)
    {
        return Inertia::render('Store/Instructors', [
            'page_title' => __('Instructors'),
            'header' => __('Instructors'),
            'instructors' => User::instructor()->get(),
        ]);
    }
}
