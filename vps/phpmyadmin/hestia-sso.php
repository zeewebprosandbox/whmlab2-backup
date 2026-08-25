<?php
/**
 * ZodPanel / Hestia PHPMyAdmin Single Sign-On (SSO) Handler
 * Supports both multi-database user SSO (zod_all=1) and single-database Hestia token SSO
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

function session_invalid() {
    global $session_name;
    $_SESSION = [];
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie($session_name, '', time() - 42000,
            $params["path"], $params["domain"],
            $params["secure"], $params["httponly"]
        );
    }
    @session_destroy();
    header("Location: index.php");
    exit();
}

// 1. Handle Logout
if (isset($_GET['logout'])) {
    session_invalid();
}

// 2. Handle ZodPanel All-Databases User SSO (from open/phpmyadmin/index.php)
if (!empty($_GET['user']) && !empty($_GET['pma_pass']) && !empty($_GET['token']) && isset($_GET['zod_all'])) {
    $user = preg_replace('/[^a-zA-Z0-9_-]/', '', $_GET['user']);
    $pma_pass = base64_decode($_GET['pma_pass']);
    $exp = intval($_GET['exp'] ?? 0);
    $token = $_GET['token'];

    // Validate expiration (within 10 minutes)
    if ($exp > 0 && abs(time() - $exp) > 600) {
        session_invalid();
    }

    // Verify token
    $expected_token = md5($user . $pma_pass . $exp . "ZODPANEL_SECRET");
    if (!hash_equals($expected_token, $token)) {
        session_invalid();
    }

    // Provision / refresh user in MariaDB
    $db_user = 'pma_' . $user;
    if ($user === 'admin' || $user === 'root') {
        $db_user = 'pma_admin';
    }

    // Set phpMyAdmin signon session variables
    $_SESSION['PMA_single_signon_user']     = $db_user;
    $_SESSION['PMA_single_signon_password'] = $pma_pass;
    $_SESSION['PMA_single_signon_host']     = 'localhost';
    $_SESSION['PMA_single_signon_port']     = '3306';
    $_SESSION['HESTIA_sso_user']            = $user;

    @session_write_close();
    header("Location: index.php");
    exit();
}

// 3. Handle Standard Hestia Single-DB Token SSO
if (!empty($_GET['user']) && !empty($_GET['hestia_token']) && !empty($_GET['database'])) {
    $user = preg_replace('/[^a-zA-Z0-9_-]/', '', $_GET['user']);
    $database = preg_replace('/[^a-zA-Z0-9_-]/', '', $_GET['database']);
    $db_user = 'pma_' . $user;
    $pma_pass = "ZodHostPass_" . $user . "_2026!";

    $_SESSION['PMA_single_signon_user']     = $db_user;
    $_SESSION['PMA_single_signon_password'] = $pma_pass;
    $_SESSION['PMA_single_signon_host']     = 'localhost';
    $_SESSION['PMA_single_signon_port']     = '3306';
    $_SESSION['HESTIA_sso_user']            = $user;
    $_SESSION['HESTIA_sso_database']        = $database;

    @session_write_close();
    header("Location: index.php?route=/database/structure&db=" . urlencode($database));
    exit();
}

// If no valid credentials, redirect to login index
header("Location: index.php");
exit();