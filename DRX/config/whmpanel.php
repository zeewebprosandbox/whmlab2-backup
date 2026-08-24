<?php

return [
    /*
    |--------------------------------------------------------------------------
    | WHMPanel Control Panel Domain
    |--------------------------------------------------------------------------
    |
    | Set this to a full hostname such as panel.example.com to serve the
    | admin/control panel from that subdomain. Leave it empty to keep the
    | local/default /admin route.
    |
    */

    'domain' => env('WHMPANEL_DOMAIN'),
    'local_api_token' => env('WHMPANEL_API_TOKEN'),
];
