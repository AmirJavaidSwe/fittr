<?php

namespace App\Http\Controllers\Store;

use App\Http\Controllers\Controller;
use App\Models\Partner\ClassPack;
use Illuminate\Http\Request;
use Inertia\Inertia;

class StoreClassPackController extends Controller
{
    public function index(Request $request)
    {
        return Inertia::render('Store/Classpacks', [
            'page_title' => __('Classpacks'),
            'header' => __('Classpacks'),
            'class_packs' => ClassPack::all(),
        ]);
    }
}
