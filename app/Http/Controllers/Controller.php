<?php

namespace App\Http\Controllers;

use App\Traits\GenericHelper;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests, GenericHelper;

    public function business()
    {
        return session('business');
    }

    public function business_seetings()
    {
        return session('business_seetings');
    }

}
