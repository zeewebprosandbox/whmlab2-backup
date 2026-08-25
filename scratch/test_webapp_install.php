<?php

require __DIR__ . '/../vendor/autoload.php';

$ssh = new \phpseclib3\Net\SSH2('169.58.176.53', 22);
$ssh->login('root', 'Remixbrown99@');

$testCode = <<<'PHP'
<?php
define('NO_AUTH_REQUIRED', true);
$_SERVER['HESTIA'] = '/usr/local/hestia';
$_SERVER['DOCUMENT_ROOT'] = '/usr/local/hestia/web';
$_SERVER['REQUEST_URI'] = '/add/webapp/?app=WordPress&domain=kids.99code.xyz';
$_SERVER['REMOTE_ADDR'] = '127.0.0.1';
$_SERVER['HTTP_USER_AGENT'] = 'Mozilla/5.0';

include_once '/usr/local/hestia/web/inc/main.php';
require_once '/usr/local/hestia/web/src/init.php';

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
$_SESSION['WEB_SYSTEM'] = 'nginx';
$_SESSION['WEB_BACKEND'] = 'php-fpm';

$domain = 'kids.99code.xyz';
$hestia = new \Hestia\System\HestiaApp();
$wpSetup = new \Hestia\WebApp\Installers\WordPress\WordPressSetup($hestia);
$wizard = new \Hestia\WebApp\AppWizard($wpSetup, $domain, $hestia);

$formData = [
    'webapp_site_name' => 'Kids 99Code Portal',
    'webapp_username' => 'wpadmin',
    'webapp_password' => 'KidsWPAdmin2026!',
    'webapp_email' => 'admin@kids.99code.xyz',
    'webapp_language' => 'en_US',
    'webapp_php_version' => '8.5',
    'webapp_database_create' => '1',
    'webapp_database_host' => 'localhost',
    'webapp_database_name' => 'kids_wp',
    'webapp_database_user' => 'kids_wp',
    'webapp_database_password' => 'DbPass2026_Kids!',
    'webapp_install_directory' => '',
];

echo "Executing AppWizard->execute()...\n";
try {
    $wizard->execute($formData);
    echo "SUCCESS: Full WordPress Quick App Installation completed seamlessly!\n";
} catch (\Throwable $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString() . "\n";
}
PHP;

$sftp = new \phpseclib3\Net\SFTP('169.58.176.53', 22);
$sftp->login('root', 'Remixbrown99@');
$sftp->put('/tmp/test_wp_full_install.php', $testCode);

echo $ssh->exec('php /tmp/test_wp_full_install.php');
