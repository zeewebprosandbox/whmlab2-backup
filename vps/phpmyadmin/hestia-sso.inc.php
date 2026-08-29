<?php
if (isset($cfg['Servers'][$i]) && !empty($cfg['Servers'][$i]['host'])) {
    if (isset($_GET['hestia_token']) || isset($_GET['zod_all']) || isset($_COOKIE['SignonSession'])) {
        $cfg['Servers'][$i]['auth_type'] = 'signon';
        $cfg['Servers'][$i]['SignonSession'] = 'SignonSession';
        $cfg['Servers'][$i]['SignonURL'] = 'hestia-sso.php';
        $cfg['Servers'][$i]['LogoutURL'] = 'hestia-sso.php?logout=1';
        $cfg['Servers'][$i]['SignonCookieParams'] = [
            'lifetime' => 0,
            'path'     => '/',
            'domain'   => '',
            'secure'   => true,
            'httponly' => true,
            'samesite' => 'Lax',
        ];
    }
}
