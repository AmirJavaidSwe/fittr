<?php

namespace App\Http\Controllers\Store;

use App\Http\Controllers\Controller;
use App\Models\Partner\User;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Str;

class StoreInstructorController extends Controller
{
    public function index(Request $request)
    {
        $instructors = User::instructor()->get()->each(function ($instructor) {
            $instructor->email = Str::mask($instructor->email, '*', 3);
        });

        return Inertia::render('Store/Instructors', [
            'page_title' => __('Instructors'),
            'header' => __('Instructors'),
            'instructors' => $instructors,
        ]);
    }
}
