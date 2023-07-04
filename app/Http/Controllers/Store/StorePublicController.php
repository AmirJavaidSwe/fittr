<?php

namespace App\Http\Controllers\Store;

use App\Http\Controllers\Controller;
use App\Models\Partner\ClassLesson;
use App\Models\Partner\Location;
use App\Models\Partner\User;
use Illuminate\Http\Request;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Inertia\Inertia;


class StorePublicController extends Controller
{
    public function index(Request $request)
    {
        if($request->filled('uid')){
            return $request->hasValidSignature() ? $this->socialLogin($request) : to_route('ss.home', ['subdomain' => $request->subdomain]);
        }
        $totalClasses = ClassLesson::count();
        $totalInstructors = User::instructor()->count();
        $totalLocations = Location::count();

        return Inertia::render('Store/Homepage', [
            'page_title' => __('Homepage'),
            'header' => __('Homepage'),
            'totalClasses' => $totalClasses,
            'totalInstructors' => $totalInstructors,
            'totalLocations' => $totalLocations,
        ]);
    }

    public function socialLogin(Request $request)
    {
        try {
            $uid = Crypt::decryptString($request->uid);
        } catch (DecryptException $e) {
            return to_route('ss.home', ['subdomain' => $request->subdomain]);
        }

        if(Auth::check() && Auth::user()->id != $uid){
            Auth::logout(); 
            session()->invalidate();
            session()->regenerateToken();
        }

        $user = Auth::loginUsingId($uid);
        $url = route($user?->dashboard_route ?? 'ss.home', ['subdomain' => $request->subdomain]);

        return redirect()->intended($url);
    }
}
