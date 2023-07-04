<?php

namespace App\Traits;

use Illuminate\Support\Str;

trait GenericHelper
{
    public function redirectBackError($msg = null)
    {
        return redirect()->back()->with('flash_type', 'error')->with('flash_message', __($msg ?? 'Error'))->with('flash_timestamp', time());
    }

    public function redirectBackWarning($msg = null)
    {
        return redirect()->back()->with('flash_type', 'warning')->with('flash_message', __($msg ?? 'Warning'))->with('flash_timestamp', time());
    }

    public function redirectBackSuccess($msg = null, $route = null)
    {
        return empty($route) ? 
            redirect()->back()->with('flash_type', 'success')->with('flash_message', __($msg ?? 'Success'))->with('flash_timestamp', time()) :
            redirect()->route($route)->with('flash_type', 'success')->with('flash_message', __($msg ?? 'Success'))->with('flash_timestamp', time());
    }

    public static function isMainDomain(): bool
    {
        $host = request()->host();
        $sub = strtok($host, '.');

        return strtolower($sub) === 'app';
    }

    public static function generateString($type = 'password', $length = 10): string
    {
        
        switch ($type) {
            case 'password':
                $special_chars = ['!', '@', '#', '$', '%', '^', '&', '*', '(', ')'];
                $lc_range = range('a', 'z');
                $uc_range = range('A', 'Z');
                $string = Str::random($length - 3);
                $string .= $special_chars[array_rand($special_chars)];
                $string .= $lc_range[array_rand($lc_range)];
                $string .= $uc_range[array_rand($uc_range)];
                break;
            
            default:
                $string = Str::random($length);
                break;
        }

        return $string;
    }
}
