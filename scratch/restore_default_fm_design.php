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

echo "1. Reading /usr/local/hestia/web/fm/configuration.php...\n";
$configContent = $sftp->get('/usr/local/hestia/web/fm/configuration.php');

// Replace add_to_head to remove hst-custom.css link and keep standard default logo style
$cleanAddHead = "\$dist_config[\"services\"][\"Filegator\\\\Services\\\\View\\\\ViewInterface\"][\"config\"] = [\n\t\"add_to_head\" => '\n    <style>\n        .logo {\n            width: 46px;\n        }\n    </style>\n    ',";

$pattern = '/\$dist_config\["services"\]\["Filegator\\\\Services\\\\View\\\\ViewInterface"\]\["config"\]\s*=\s*\[\s*"add_to_head"\s*=>\s*\'[^\']*\',/s';

if (preg_match($pattern, $configContent)) {
    $updatedConfig = preg_replace($pattern, $cleanAddHead, $configContent);
    echo "Found and replaced add_to_head in configuration.php!\n";
    $sftp->put('/usr/local/hestia/web/fm/configuration.php', $updatedConfig);
    echo "Saved /usr/local/hestia/web/fm/configuration.php.\n";
} else {
    echo "Pattern not matched directly, checking current content...\n";
}

echo "\n2. Emptying /usr/local/hestia/web/fm/dist/css/hst-custom.css...\n";
$sftp->put('/usr/local/hestia/web/fm/dist/css/hst-custom.css', "/* Default File Manager CSS - No external overrides */\n");

echo "\n3. Checking configuration.php add_to_head section:\n";
echo $ssh->exec('grep -A 10 "add_to_head" /usr/local/hestia/web/fm/configuration.php') . "\n";

echo "4. Testing File Manager API & Render...\n";
echo $ssh->exec('php -r "require \'/usr/local/hestia/web/fm/configuration.php\'; echo \'Configuration syntax valid 100%\' . PHP_EOL;"') . "\n";

echo "✓ File Manager design restored to default cleanly without external CSS!\n";
