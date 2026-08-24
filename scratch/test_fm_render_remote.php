<?php

require __DIR__ . '/../vendor/autoload.php';

$ssh = new \phpseclib3\Net\SSH2('169.58.176.53', 22);
$ssh->login('root', 'Remixbrown99@');

$sftp = new \phpseclib3\Net\SFTP('169.58.176.53', 22);
$sftp->login('root', 'Remixbrown99@');

$testScript = <<<'PHP'
<?php
define('NO_AUTH_REQUIRED', true);
$_SERVER['HESTIA'] = '/usr/local/hestia';
$_SERVER['DOCUMENT_ROOT'] = '/usr/local/hestia/web';
$_SERVER['REQUEST_URI'] = '/fm/domains/';
$_SERVER['REMOTE_ADDR'] = '127.0.0.1';
$_SERVER['HTTP_USER_AGENT'] = 'Mozilla/5.0';

include_once '/usr/local/hestia/web/inc/main.php';

$_SESSION['LANGUAGE'] = 'en';
$_SESSION['language'] = 'en';
$_SESSION['user'] = 'zodhost';
$_SESSION['token'] = 'test_token';
$_SESSION['userContext'] = 'user';
$_SESSION['userSortOrder'] = 'name';
$_SESSION['look'] = '';
$_SESSION['POLICY_SYSTEM_ENABLE_THEME_CUSTOMIZATION'] = 'no';
$_SESSION['THEME'] = 'zodpanel';
$_SESSION['user_combined_ip'] = get_real_user_ip();
$_SESSION['VERSION'] = '1.10.2';

$user = 'zodhost';
$TAB = 'FM';

$fm_user = $user;
exec(HESTIA_CMD . "v-list-web-domains " . escapeshellarg($fm_user) . " 'json'", $output, $return_var);
$data = json_decode(implode("", $output), true) ?: [];

exec(HESTIA_CMD . "v-list-user " . escapeshellarg($fm_user) . " 'json'", $user_output, $user_return_var);
$user_data = json_decode(implode("", $user_output), true)[$fm_user] ?? [];

ob_start();
render_page($user, $TAB, "list_fm_domains");
$pageOut = ob_get_clean();

echo "RENDER SUCCESS! HTML Output length: " . strlen($pageOut) . "\n";
echo "Contains 'Choose Website or Storage Directory': " . (str_contains($pageOut, "Choose Website") ? "YES" : "NO") . "\n";
echo "Contains 'kids.99code.xyz': " . (str_contains($pageOut, "kids.99code.xyz") ? "YES" : "NO") . "\n";
echo "Contains 'Default Website': " . (str_contains($pageOut, "Default Website") ? "YES" : "NO") . "\n";
PHP;

$sftp->put('/tmp/test_fm_render.php', $testScript);
echo $ssh->exec('php /tmp/test_fm_render.php');
