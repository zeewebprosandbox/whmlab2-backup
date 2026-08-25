<?php

require __DIR__ . '/../vendor/autoload.php';

$ssh = new \phpseclib3\Net\SSH2('169.58.176.53', 22);
$ssh->login('root', 'Remixbrown99@');

$sftp = new \phpseclib3\Net\SFTP('169.58.176.53', 22);
$sftp->login('root', 'Remixbrown99@');

$pyCode = file_get_contents(__DIR__ . '/repair_mail_deliverability.py');
$sftp->put('/usr/local/hestia/bin/v-zodpanel-repair-mail-deliverability', $pyCode);
$ssh->exec('chmod 755 /usr/local/hestia/bin/v-zodpanel-repair-mail-deliverability');

echo "=== EXECUTING SCRIPT ===\n";
echo $ssh->exec('python3 -u /usr/local/hestia/bin/v-zodpanel-repair-mail-deliverability 2>&1');
echo "\n=== FINISHED ===\n";
