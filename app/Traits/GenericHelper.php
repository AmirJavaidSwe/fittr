<?php

namespace App\Traits;

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

    public function redirectBackSuccess($msg = null)
    {
        return redirect()->back()->with('flash_type', 'success')->with('flash_message', __($msg ?? 'Success'))->with('flash_timestamp', time());
    }
}
