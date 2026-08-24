<?php

namespace App\Http\Controllers;

class ControlPanelRedirectController extends Controller
{
    public function __invoke()
    {
        $domain = config('whmpanel.domain');

        abort_if(!$domain, 404);

        return redirect()->away(request()->getScheme() . '://' . $domain);
    }
}
