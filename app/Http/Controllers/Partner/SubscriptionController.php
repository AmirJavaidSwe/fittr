<?php

namespace App\Http\Controllers\Partner;

use App\Enums\Subscription as Enum;
use App\Http\Controllers\Controller;
use App\Http\Requests\Partner\SubscriptionRequest;
use App\Models\Package;
use App\Models\Subscription;
use App\Events\SubscriptionCancelled;
use App\Events\SubscriptionChanged;
use App\Events\SubscriptionStarted;
use Illuminate\Http\Request;
use Inertia\Inertia;

class SubscriptionController extends Controller
{
    public function index(Request $request)
    {
        $partner = $request->user();
        $active_subscription = $partner->active_subscription;
        $packages = Package::public()->get()->filter(function($package) use ($active_subscription) {
            // filter out current package subscribed to
            return !$active_subscription || $active_subscription->package_id != $package->id;
        })
        //reset array keys for json:
        ->values();

        return Inertia::render('Partner/Subscription', [
            'page_title' => __('Subscription'),
            'header' => __('Subscription'),
            'packages' => $packages,
            'active_subscription' => $active_subscription,
            'has_subscription' => !empty($active_subscription),
        ]);
    }

    public function cancel(Request $request, Subscription $subscription)
    {
        $this->authorize('update', $subscription);
        $subscription->update(['status' => Enum::CANCELLED->value, 'cancelled_at' => now()]);

        SubscriptionCancelled::dispatch($subscription);

        return redirect()->route('partner.subscriptions.index')->with('flash_type', 'success')->with('flash_timestamp', time());
    }

    public function store(SubscriptionRequest $request, Package $package)
    {
        $partner = $request->user();
        $active_subscription = $partner->active_subscription;
        if($active_subscription){
            $active_subscription->update(['status' => Enum::CANCELLED->value, 'cancelled_at' => now()]);
        }

        $fee = match ($request->period) {
            Enum::MONTH->value => $package->is_free ? 0 : $package->fee_monthly,
            Enum::YEAR->value => $package->annual_price_year,
        };

        /*
            Subscription inherits base package props at creation and no longer depends on parent package.
            Admin should be able to amend any individual subscription when needed.
            We may also watch for changes and differences between subscription and parent package, optionally highlighting the pack when both are not exact.
        */
        $subscription = Subscription::create([
            'status' => Enum::ACTIVE->value,
            'package_title' => $package->title,
            'package_id' => $package->id,
            'partner_id' => $partner->id,
            'cycle' => $request->period,
            'tx_percent' => $package->tx_percent,
            'tx_fixed_fee' => $package->tx_fixed_fee,
            'fee' => $fee,
            'monthly_fee_sites' => $package->monthly_fee_sites,
            'admin_users' => $package->admin_users,
            'max_sites' => $package->max_sites,
        ]);

        SubscriptionChanged::dispatchIf($active_subscription, $active_subscription, $subscription);
        SubscriptionStarted::dispatchIf(!$active_subscription, $subscription);

        return redirect()->route('partner.subscriptions.index')->with('flash_type', 'success')->with('flash_timestamp', time());
    }
}
