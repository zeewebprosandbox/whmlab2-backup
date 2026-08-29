<?php
/**
 * ZodPanel / Hestia PHPMyAdmin Single Sign-On (SSO) Handler
 */

error_reporting(0);
ini_set('display_errors', '0');

$session_name = 'SignonSession';
session_name($session_name);
session_set_cookie_params([
    'lifetime' => 0,
    'path'     => '/',
    'domain'   => '',
    'secure'   => true,
    'httponly' => true,
    'samesite' => 'Lax',
]);

@session_start();

function clear_all_pma_cookies() {
    global $session_name;
    $_SESSION = [];
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie($session_name, '', time() - 42000, $params["path"], $params["domain"], $params["secure"], $params["httponly"]);
        setcookie($session_name, '', time() - 42000, '/');
        setcookie($session_name, '', time() - 42000, '/phpmyadmin/');
    }
    setcookie('phpMyAdmin', '', time() - 42000, '/');
    setcookie('phpMyAdmin', '', time() - 42000, '/phpmyadmin/');
    setcookie('__Secure-phpMyAdmin_https', '', time() - 42000, '/', '', true, true);
    setcookie('__Secure-phpMyAdmin_https', '', time() - 42000, '/phpmyadmin/', '', true, true);
    @session_destroy();
}

// 1. Handle Logout
if (isset($_GET['logout'])) {
    clear_all_pma_cookies();
    header("Location: index.php");
    exit();
}

// 2. Handle ZodPanel All-Databases / Specific-Database SSO
if (!empty($_GET['user']) && !empty($_GET['pma_pass']) && !empty($_GET['token']) && isset($_GET['zod_all'])) {
    $user = preg_replace('/[^a-zA-Z0-9_-]/', '', $_GET['user']);
    $pma_pass = base64_decode($_GET['pma_pass']);
    $exp = intval($_GET['exp'] ?? 0);
    $token = $_GET['token'];

    // Validate expiration (within 10 minutes)
    if ($exp > 0 && abs(time() - $exp) > 600) {
        clear_all_pma_cookies();
        header("Location: index.php");
        exit();
    }

    // Verify token
    $expected_token = md5($user . $pma_pass . $exp . "ZODPANEL_SECRET");
    if (!hash_equals($expected_token, $token)) {
        clear_all_pma_cookies();
        header("Location: index.php");
        exit();
    }

    $db_user = 'pma_' . $user;

    // Set phpMyAdmin signon session variables
    $_SESSION['PMA_single_signon_user']     = $db_user;
    $_SESSION['PMA_single_signon_password'] = $pma_pass;
    $_SESSION['PMA_single_signon_host']     = 'localhost';
    $_SESSION['PMA_single_signon_port']     = '3306';
    $_SESSION['HESTIA_sso_user']            = $user;

    // Clear old phpMyAdmin session cookies so phpMyAdmin starts a clean authenticated session
    setcookie('phpMyAdmin', '', time() - 42000, '/');
    setcookie('phpMyAdmin', '', time() - 42000, '/phpmyadmin/');
    setcookie('__Secure-phpMyAdmin_https', '', time() - 42000, '/', '', true, true);
    setcookie('__Secure-phpMyAdmin_https', '', time() - 42000, '/phpmyadmin/', '', true, true);

    @session_write_close();

    $redirectUrl = 'index.php';
    if (!empty($_GET['database'])) {
        $db = preg_replace('/[^a-zA-Z0-9_-]/', '', $_GET['database']);
        $redirectUrl = 'index.php?route=/database/structure&db=' . urlencode($db);
    }
    header("Location: " . $redirectUrl);
    exit();
}

// 3. Fallback for unauthenticated direct hits to hestia-sso.php
clear_all_pma_cookies();
header("Location: index.php");
exit();