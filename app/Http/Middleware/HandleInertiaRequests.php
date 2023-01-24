<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Inertia\Middleware;
use Route;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that's loaded on the first page visit.
     *
     * @see https://inertiajs.com/server-side-setup#root-template
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determines the current asset version.
     *
     * @see https://inertiajs.com/asset-versioning
     * @param  \Illuminate\Http\Request  $request
     * @return string|null
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Defines the props that are shared by default.
     *
     * @see https://inertiajs.com/shared-data
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function share(Request $request): array
    {
        return array_merge(parent::share($request), [
            'app_name' => config('app.name'),
            'route_name' => Route::currentRouteName(),
            'menu' => $this->navRoutes($request->user()),
            'flash' => [
                'type' => $request->session()->get('flash_type'),
                'message' => $request->session()->get('flash_message', __('OK')),
                'timestamp' => $request->session()->get('flash_timestamp'),
            ],
        ]);
    }

    public function navRoutes($user): array
    {
        if(empty($user)){
            return [];
        }

        if($user->is_partner){
            return array(
                [
                    'title' => 'Dashboard',
                    'icon' => 'fa-solid fa-home',
                    'route_name' => 'partner.dashboard',
                ],
                [
                    'title' => 'Pricing',
                    'icon' => 'fa-solid fa-gears',
                    'route_name' => 'partner.pricing.index',
                ],
            );
        }

        if($user->is_admin){
            return array(
                [
                    'title' => 'Dashboard',
                    'icon' => 'fa-solid fa-home',
                    'route_name' => 'admin.dashboard',
                ],
                [
                    'title' => 'Partner performance',
                    'icon' => 'fa-solid fa-gauge-high',
                    'route_name' => 'admin.partners.performance.index',
                ],
                [
                    'title' => 'Partner management',
                    'icon' => 'fa-solid fa-user-tie',
                    'route_name' => 'admin.partners.index',
                ],
                [
                    'title' => 'Settings',
                    'icon' => 'fa-solid fa-gears',
                    'route_name' => 'admin.settings',
                ],
                [
                    'title' => 'Typography',
                    'icon' => 'fa-solid fa-coffee',
                    'route_name' => 'admin.demo',
                ],
            );
        }
    }
}
