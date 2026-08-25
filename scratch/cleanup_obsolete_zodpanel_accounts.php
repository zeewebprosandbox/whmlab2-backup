<?php

require __DIR__ . '/../vendor/autoload.php';

// Bootstrap Laravel in oldtemplate
$app = require_once __DIR__ . '/../oldtemplate/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$validUsernames = \App\Models\Hosting::pluck('username')->map(fn($u) => trim(strtolower($u)))->filter()->toArray();
$validUsernames[] = 'admin';

echo "=== VALID USERNAMES FROM WHMLAB ADMIN ===" . PHP_EOL;
print_r($validUsernames);

$ssh = new \phpseclib3\Net\SSH2('169.58.176.53', 22);
if (!$ssh->login('root', 'Remixbrown99@')) {
    die("SSH login failed\n");
}

$rawUsers = $ssh->exec('/usr/local/hestia/bin/v-list-users json');
$remoteUsers = json_decode($rawUsers, true) ?: [];

echo PHP_EOL . "=== REMOTE ZODPANEL USERS DETECTED ===" . PHP_EOL;
print_r(array_keys($remoteUsers));

$toDelete = [];
foreach (array_keys($remoteUsers) as $remoteUser) {
    if (!in_array(strtolower($remoteUser), $validUsernames)) {
        $toDelete[] = $remoteUser;
    }
}

echo PHP_EOL . "=== ACCOUNTS TO DELETE PERMANENTLY ===" . PHP_EOL;
print_r($toDelete);

foreach ($toDelete as $delUser) {
    echo "Deleting remote user '{$delUser}'... ";
    $res = $ssh->exec('/usr/local/hestia/bin/v-delete-user ' . escapeshellarg($delUser));
    echo "DONE. (" . trim($res) . ")" . PHP_EOL;
}

echo PHP_EOL . "=== REMAINING REMOTE USERS AFTER PURGE ===" . PHP_EOL;
$remainingRaw = $ssh->exec('/usr/local/hestia/bin/v-list-users json');
$remaining = json_decode($remainingRaw, true) ?: [];
print_r(array_keys($remaining));

// Update current_accounts on Server in WHMLab
$activeCount = \App\Models\Hosting::where('status', 1)->count();
\App\Models\Server::query()->update(['current_accounts' => $activeCount]);
echo "Updated WHMLab server current_accounts to: {$activeCount}" . PHP_EOL;

echo "✓ Purge completed successfully!" . PHP_EOL;
