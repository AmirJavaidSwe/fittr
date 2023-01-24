<?php

namespace App\Http\Controllers\Partner;

use App\Http\Controllers\Controller;
use App\Models\Package;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PricingController extends Controller
{
    public function index(Request $request)
    {
        $partner = $request->user();
        $active_subscription = $partner->active_subscription;
        $packages = Package::public()->get()->each(function($package) use ($active_subscription) {
            $package->subscribed = !empty($active_subscription) && $active_subscription->package_id == $package->id;
        });

        return Inertia::render('Partner/Pricing', [
            'page_title' => __('Pricing'),
            'header' => __('Pricing'),
            'packages' => $packages,
            'has_subscription' => !empty($active_subscription),
        ]);
    }
}
