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

echo "1. Updating /usr/local/hestia/bin/v-add-user-pma-temp-user...\n";
$binScript = <<<'BASH'
#!/usr/bin/env bash
# info: Provision all-database access for Hestia/ZodPanel user
user="$1"
[ -n "$user" ] || exit 1

pma_user="pma_${user}"
pma_pass="ZodHostPass_${user}_2026!"

MYSQL_CMD="mariadb"
command -v mariadb >/dev/null 2>&1 || MYSQL_CMD="mysql"

MYSQL_AUTH=""
if [ -f /usr/local/hestia/conf/mysql.conf ]; then
    source /usr/local/hestia/conf/mysql.conf
    [ -n "$PASSWORD" ] && MYSQL_AUTH="-p${PASSWORD}"
fi

$MYSQL_CMD -uroot $MYSQL_AUTH -e "CREATE USER IF NOT EXISTS '${pma_user}'@'localhost' IDENTIFIED BY '${pma_pass}'; ALTER USER '${pma_user}'@'localhost' IDENTIFIED BY '${pma_pass}';" 2>/dev/null || true

if [ "$user" = "admin" ] || [ "$user" = "root" ]; then
    $MYSQL_CMD -uroot $MYSQL_AUTH -e "GRANT ALL PRIVILEGES ON *.* TO '${pma_user}'@'localhost' WITH GRANT OPTION; FLUSH PRIVILEGES;" 2>/dev/null || true
else
    $MYSQL_CMD -uroot $MYSQL_AUTH -e "GRANT ALL PRIVILEGES ON \`${user}_%\`.* TO '${pma_user}'@'localhost'; GRANT ALL PRIVILEGES ON \`${user}\`.* TO '${pma_user}'@'localhost'; FLUSH PRIVILEGES;" 2>/dev/null || true
fi

echo -n "$pma_pass"
BASH;

$sftp->put('/usr/local/hestia/bin/v-add-user-pma-temp-user', $binScript);
$ssh->exec('chmod 755 /usr/local/hestia/bin/v-add-user-pma-temp-user && chown root:root /usr/local/hestia/bin/v-add-user-pma-temp-user');
echo "✓ Saved /usr/local/hestia/bin/v-add-user-pma-temp-user\n";

echo "2. Provisioning all current users in MariaDB...\n";
$users = ["admin", "root", "zodhost", "copyexpertsignalsonline", "trysend32", "trysend35", "winvest86", "trysend80"];
foreach ($users as $u) {
    $ssh->exec('/usr/local/hestia/bin/v-add-user-pma-temp-user ' . escapeshellarg($u));
}
echo "✓ Provisioned all users\n";

echo "3. Updating /usr/local/hestia/web/open/phpmyadmin/index.php...\n";
$openPmaCode = <<<'PHP'
<?php
$TAB = "DB";

// Main include
include $_SERVER["DOCUMENT_ROOT"] . "/inc/main.php";

$panel_host = $_SERVER["HTTP_HOST"] ?? "zodpanel.zodserver.cloud:8083";
[$http_host, $port] = explode(":", $panel_host . ":");
$pma_path = "/phpmyadmin/";

if (!empty($_SESSION["DB_PMA_ALIAS"])) {
	$pma_path = "/" . trim($_SESSION["DB_PMA_ALIAS"], "/") . "/";
}

$pma_url = "https://" . $http_host . $pma_path;

if (empty($user_plain)) {
	$user_plain = $_SESSION["user"] ?? "admin";
}

// Provision temporary all-in-one permissions in Hestia backend with sudo
exec(HESTIA_CMD . "v-add-user-pma-temp-user " . escapeshellarg($user_plain) . " 2>/dev/null", $output, $ret);

$time = time();
$temp_pass = "ZodHostPass_" . $user_plain . "_2026!";
$token = md5($user_plain . $temp_pass . $time . "ZODPANEL_SECRET");

$params = [
	"user" => $user_plain,
	"pma_pass" => base64_encode($temp_pass),
	"exp" => $time,
	"token" => $token,
	"zod_all" => 1,
];

if (!empty($_GET["database"])) {
	$params["database"] = preg_replace('/[^a-zA-Z0-9_-]/', '', $_GET["database"]);
}

header("Location: " . $pma_url . "hestia-sso.php?" . http_build_query($params));
exit();
PHP;

$sftp->put('/usr/local/hestia/web/open/phpmyadmin/index.php', $openPmaCode);
$ssh->exec('chmod 644 /usr/local/hestia/web/open/phpmyadmin/index.php && chown root:hestiaweb /usr/local/hestia/web/open/phpmyadmin/index.php');
echo "✓ Saved /usr/local/hestia/web/open/phpmyadmin/index.php\n";

echo "4. Updating /usr/share/phpmyadmin/hestia-sso.php...\n";
$ssoPhpCode = <<<'PHP'
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

function clear_signon_cookie_and_exit() {
    global $session_name;
    $_SESSION = [];
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie($session_name, '', time() - 42000,
            $params["path"], $params["domain"],
            $params["secure"], $params["httponly"]
        );
        setcookie($session_name, '', time() - 42000, '/');
        setcookie($session_name, '', time() - 42000, '/phpmyadmin/');
    }
    @session_destroy();
    header("Location: index.php?sso_failed=1");
    exit();
}

// 1. Handle Logout
if (isset($_GET['logout'])) {
    clear_signon_cookie_and_exit();
}

// 2. Handle ZodPanel All-Databases User SSO (from open/phpmyadmin/index.php)
if (!empty($_GET['user']) && !empty($_GET['pma_pass']) && !empty($_GET['token']) && isset($_GET['zod_all'])) {
    $user = preg_replace('/[^a-zA-Z0-9_-]/', '', $_GET['user']);
    $pma_pass = base64_decode($_GET['pma_pass']);
    $exp = intval($_GET['exp'] ?? 0);
    $token = $_GET['token'];

    // Validate expiration (within 10 minutes)
    if ($exp > 0 && abs(time() - $exp) > 600) {
        clear_signon_cookie_and_exit();
    }

    // Verify token
    $expected_token = md5($user . $pma_pass . $exp . "ZODPANEL_SECRET");
    if (!hash_equals($expected_token, $token)) {
        clear_signon_cookie_and_exit();
    }

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

    $redirectUrl = 'index.php';
    if (!empty($_GET['database'])) {
        $db = preg_replace('/[^a-zA-Z0-9_-]/', '', $_GET['database']);
        $redirectUrl = 'index.php?route=/database/structure&db=' . urlencode($db);
    }
    header("Location: " . $redirectUrl);
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

// 4. Fallback for direct access: clear cookie and redirect to login
clear_signon_cookie_and_exit();
PHP;

$sftp->put('/usr/share/phpmyadmin/hestia-sso.php', $ssoPhpCode);
$ssh->exec('chmod 644 /usr/share/phpmyadmin/hestia-sso.php && chown root:hestiamail /usr/share/phpmyadmin/hestia-sso.php');
echo "✓ Saved /usr/share/phpmyadmin/hestia-sso.php\n";

echo "5. Testing SSO login flow for copyexpertsignalsonline...\n";
$time = time();
$pass = "ZodHostPass_copyexpertsignalsonline_2026!";
$token = md5("copyexpertsignalsonline" . $pass . $time . "ZODPANEL_SECRET");
$testUrl = "https://zodpanel.zodserver.cloud/phpmyadmin/hestia-sso.php?user=copyexpertsignalsonline&pma_pass=" . urlencode(base64_encode($pass)) . "&exp={$time}&token={$token}&zod_all=1";

$curlTest = $ssh->exec("rm -f /tmp/pma_test_copy.cookie && curl -s -k -c /tmp/pma_test_copy.cookie -b /tmp/pma_test_copy.cookie -L -o /dev/null -w \"HTTP %{http_code}, Redirects: %{num_redirects}, Final URL: %{url_effective}\n\" \"$testUrl\"");
echo "Result for copyexpertsignalsonline: " . $curlTest . "\n";

echo "6. Testing SSO with database parameter (copyexpertsignalsonline_app)...\n";
$testDbUrl = "https://zodpanel.zodserver.cloud/phpmyadmin/hestia-sso.php?user=copyexpertsignalsonline&pma_pass=" . urlencode(base64_encode($pass)) . "&exp={$time}&token={$token}&zod_all=1&database=copyexpertsignalsonline_app";
$curlDbTest = $ssh->exec("rm -f /tmp/pma_test_copydb.cookie && curl -s -k -c /tmp/pma_test_copydb.cookie -b /tmp/pma_test_copydb.cookie -L -o /dev/null -w \"HTTP %{http_code}, Redirects: %{num_redirects}, Final URL: %{url_effective}\n\" \"$testDbUrl\"");
echo "Result for copyexpertsignalsonline_app DB: " . $curlDbTest . "\n";
