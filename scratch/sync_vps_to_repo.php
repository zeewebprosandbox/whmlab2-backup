<?php

require __DIR__ . '/../vendor/autoload.php';

$ssh = new \phpseclib3\Net\SSH2('169.58.176.53', 22);
if (!$ssh->login('root', 'Remixbrown99@')) {
    die("SSH Failed\n");
}

$sftp = new \phpseclib3\Net\SFTP('169.58.176.53', 22);
if (!$sftp->login('root', 'Remixbrown99@')) {
    die("SFTP Failed\n");
}

$baseDir = __DIR__ . '/../vps';
@mkdir($baseDir, 0755, true);
@mkdir($baseDir . '/usr_local_hestia_bin', 0755, true);
@mkdir($baseDir . '/etc_whmpanel', 0755, true);
@mkdir($baseDir . '/etc_nginx_custom', 0755, true);
@mkdir($baseDir . '/roundcube_plugins/zodpanel_sso', 0755, true);
@mkdir($baseDir . '/phpmyadmin', 0755, true);
@mkdir($baseDir . '/dovecot_configs', 0755, true);

echo "1. Archiving /usr/local/hestia/web on remote VPS...\n";
$ssh->exec("tar -czf /tmp/hestia_web.tar.gz -C /usr/local/hestia web");

echo "2. Downloading /tmp/hestia_web.tar.gz to local repository...\n";
$sftp->get('/tmp/hestia_web.tar.gz', $baseDir . '/hestia_web.tar.gz');
$ssh->exec("rm -f /tmp/hestia_web.tar.gz");

echo "3. Extracting hestia_web.tar.gz locally...\n";
shell_exec("tar -xzf " . escapeshellarg($baseDir . '/hestia_web.tar.gz') . " -C " . escapeshellarg($baseDir) . " && rm -f " . escapeshellarg($baseDir . '/hestia_web.tar.gz'));

echo "4. Downloading custom binaries from /usr/local/hestia/bin/...\n";
$customBins = [
    'v-zodpanel-node-app',
    'v-zodpanel-git-deploy',
    'v-zodpanel-repair-mail-deliverability',
    'v-zodpanel-run-domain-command',
    'v-zodpanel-save-package-features',
    'v-zodpanel-snapshot',
    'v-add-user-pma-temp-user',
    'v-run-cli-cmd',
    'v-add-mail-account',
    'v-delete-mail-account',
    'v-add-web-domain',
    'v-delete-web-domain',
];

foreach ($customBins as $bin) {
    $remotePath = "/usr/local/hestia/bin/$bin";
    if ($sftp->file_exists($remotePath)) {
        $sftp->get($remotePath, $baseDir . "/usr_local_hestia_bin/$bin");
        chmod($baseDir . "/usr_local_hestia_bin/$bin", 0755);
        echo "  - Downloaded: $bin\n";
    }
}

echo "5. Downloading /etc/whmpanel configs...\n";
$files = $sftp->nlist('/etc/whmpanel');
if ($files) {
    foreach ($files as $f) {
        if ($f !== '.' && $f !== '..') {
            $sftp->get("/etc/whmpanel/$f", $baseDir . "/etc_whmpanel/$f");
            echo "  - Downloaded /etc/whmpanel/$f\n";
        }
    }
}

echo "6. Downloading Roundcube zodpanel_sso plugin...\n";
$sftp->get('/var/lib/roundcube/plugins/zodpanel_sso/zodpanel_sso.php', $baseDir . '/roundcube_plugins/zodpanel_sso/zodpanel_sso.php');
$sftp->get('/etc/roundcube/config.inc.php', $baseDir . '/roundcube_plugins/config.inc.php.sample');

echo "7. Downloading phpMyAdmin SSO script...\n";
$sftp->get('/usr/share/phpmyadmin/hestia-sso.php', $baseDir . '/phpmyadmin/hestia-sso.php');

echo "8. Downloading Nginx & Dovecot configurations...\n";
$sftp->get('/etc/nginx/conf.d/phpmyadmin.inc', $baseDir . '/etc_nginx_custom/phpmyadmin.inc');
$sftp->get('/etc/nginx/conf.d/cloudflare.inc', $baseDir . '/etc_nginx_custom/cloudflare.inc');
$sftp->get('/etc/dovecot/conf.d/auth-master.conf.ext', $baseDir . '/dovecot_configs/auth-master.conf.ext');

echo "Sync completed successfully.\n";
