<?php

namespace App\Http\Controllers\Store;

use App\Http\Controllers\Controller;
use App\Models\Partner\ClassLesson;
use Illuminate\Http\Request;
use Inertia\Inertia;

class StoreClassController extends Controller
{
    public function index(Request $request)
    {
        return Inertia::render('Store/Classes', [
            'page_title' => __('Classes'),
            'header' => __('Classes'),
            'classes' => ClassLesson::active()->with(['studio', 'instructor', 'classType'])->get(),
        ]);
    }
}
