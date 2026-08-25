<?php

require __DIR__ . '/../vendor/autoload.php';

$ssh = new \phpseclib3\Net\SSH2('169.58.176.53', 22);
if (!$ssh->login('root', 'Remixbrown99@')) {
    die("SSH login failed\n");
}

$sftp = new \phpseclib3\Net\SFTP('169.58.176.53', 22);
if (!$sftp->login('root', 'Remixbrown99@')) {
    die("SFTP login failed\n");
}

echo "1. Deploying /usr/share/phpmyadmin/hestia-sso.php...\n";

$ssoPhpCode = <<<'PHP'
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
PHP;

$sftp->put('/usr/share/phpmyadmin/hestia-sso.php', $ssoPhpCode);
$ssh->exec('chmod 644 /usr/share/phpmyadmin/hestia-sso.php && chown root:hestiamail /usr/share/phpmyadmin/hestia-sso.php');
echo "✓ Saved /usr/share/phpmyadmin/hestia-sso.php\n";

echo "\n2. Updating /etc/phpmyadmin/conf.d/00-hestia-sso.php...\n";
$confDCode = <<<'PHP'
<?php
if (!isset($cfg['Servers'][$i])) { $cfg['Servers'][$i] = []; }
$cfg['Servers'][$i]['auth_type'] = 'signon';
$cfg['Servers'][$i]['SignonSession'] = 'SignonSession';
$cfg['Servers'][$i]['SignonURL'] = 'hestia-sso.php';
$cfg['Servers'][$i]['LogoutURL'] = 'hestia-sso.php?logout=1';
$cfg['Servers'][$i]['host'] = 'localhost';
$cfg['Servers'][$i]['connect_type'] = 'tcp';
$cfg['Servers'][$i]['compress'] = false;
$cfg['Servers'][$i]['AllowNoPassword'] = false;
PHP;

$sftp->put('/etc/phpmyadmin/conf.d/00-hestia-sso.php', $confDCode);
$ssh->exec('chmod 644 /etc/phpmyadmin/conf.d/00-hestia-sso.php && chown root:hestiamail /etc/phpmyadmin/conf.d/00-hestia-sso.php');
echo "✓ Saved /etc/phpmyadmin/conf.d/00-hestia-sso.php\n";

echo "\n3. Ensuring /etc/nginx/conf.d/phpmyadmin.inc is included in zodpanel.zodserver.cloud vhost...\n";

$vhostSsl = $sftp->get('/home/admin/conf/web/zodpanel.zodserver.cloud/nginx.ssl.conf');
if (!str_contains($vhostSsl, 'phpmyadmin.inc')) {
    // Add include before location /
    $vhostSsl = str_replace(
        'location / {',
        "include /etc/nginx/conf.d/phpmyadmin.inc;\n\n\tlocation / {",
        $vhostSsl
    );
    $sftp->put('/home/admin/conf/web/zodpanel.zodserver.cloud/nginx.ssl.conf', $vhostSsl);
    echo "✓ Added phpmyadmin.inc include to zodpanel.zodserver.cloud nginx.ssl.conf\n";
} else {
    echo "phpmyadmin.inc already included in nginx.ssl.conf\n";
}

$vhostNonSsl = $sftp->get('/home/admin/conf/web/zodpanel.zodserver.cloud/nginx.conf');
if ($vhostNonSsl && !str_contains($vhostNonSsl, 'phpmyadmin.inc')) {
    $vhostNonSsl = str_replace(
        'location / {',
        "include /etc/nginx/conf.d/phpmyadmin.inc;\n\n\tlocation / {",
        $vhostNonSsl
    );
    $sftp->put('/home/admin/conf/web/zodpanel.zodserver.cloud/nginx.conf', $vhostNonSsl);
    echo "✓ Added phpmyadmin.inc include to zodpanel.zodserver.cloud nginx.conf\n";
}

echo "\n4. Testing Nginx configuration and reloading...\n";
$nginxTest = $ssh->exec('nginx -t');
echo $nginxTest . "\n";
$ssh->exec('systemctl reload nginx');

echo "\n5. Testing HTTP GET to https://zodpanel.zodserver.cloud/phpmyadmin/hestia-sso.php...\n";
$time = time();
$tempPass = "ZodHostPass_zodhost_2026!";
$token = md5("zodhost" . $tempPass . $time . "ZODPANEL_SECRET");
$testUrl = "https://zodpanel.zodserver.cloud/phpmyadmin/hestia-sso.php?user=zodhost&pma_pass=" . urlencode(base64_encode($tempPass)) . "&exp={$time}&token={$token}&zod_all=1";

echo "Testing URL: {$testUrl}\n";
$curlTest = $ssh->exec('curl -s -k -I ' . escapeshellarg($testUrl));
echo $curlTest . "\n";

echo "✓ phpMyAdmin SSO configuration completed!\n";
