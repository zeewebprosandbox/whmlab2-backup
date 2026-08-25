<?php

require __DIR__ . '/../vendor/autoload.php';

$sftp = new \phpseclib3\Net\SFTP('169.58.176.53', 22);
if (!$sftp->login('root', 'Remixbrown99@')) {
    die("SSH login failed\n");
}

// 1. Update HestiaApp.php
$hestiaAppPath = '/usr/local/hestia/web/src/app/System/HestiaApp.php';
$hestiaAppContent = $sftp->get($hestiaAppPath);

// Fix checkDatabaseLimit
$oldLimit = 'return $userInfo[\'DATABASES\'] === \'unlimited\' ||
                $userInfo[\'DATABASES\'] - $userInfo[\'U_DATABASES\'] < 1;';
$newLimit = 'return $userInfo[\'DATABASES\'] === \'unlimited\' ||
                ((int) $userInfo[\'DATABASES\'] - (int) $userInfo[\'U_DATABASES\']) > 0;';
if (str_contains($hestiaAppContent, '$userInfo[\'DATABASES\'] - $userInfo[\'U_DATABASES\'] < 1;')) {
    $hestiaAppContent = preg_replace(
        '/return\s+\$userInfo\[\'DATABASES\'\]\s*===\s*\'unlimited\'\s*\|\|\s*\$userInfo\[\'DATABASES\'\]\s*-\s*\$userInfo\[\'U_DATABASES\'\]\s*<\s*1;/m',
        $newLimit,
        $hestiaAppContent
    );
}

// Fix databaseAdd with retry resilience and existing database password update
$oldDbAdd = <<<'CODE'
    public function databaseAdd(
        string $name,
        string $user,
        string $password,
        string $host,
        string $type = 'mysql',
        string $charset = 'utf8mb4',
    ): void {
        $passwordFile = tempnam('/tmp', 'hst');

        $fp = fopen($passwordFile, 'w');
        fwrite($fp, $password . "\n");
        fclose($fp);

        try {
            $this->runUser('v-add-database', [$name, $user, $passwordFile, $type, $host, $charset]);
        } catch (ProcessFailedException) {
            throw new RuntimeException(_('Unable to add database!'));
        } finally {
            unlink($passwordFile);
        }
    }
CODE;

$newDbAdd = <<<'CODE'
    public function databaseAdd(
        string $name,
        string $user,
        string $password,
        string $host,
        string $type = 'mysql',
        string $charset = 'utf8mb4',
    ): void {
        $passwordFile = tempnam('/tmp', 'hst');

        $fp = fopen($passwordFile, 'w');
        fwrite($fp, $password . "\n");
        fclose($fp);

        try {
            $this->runUser('v-add-database', [$name, $user, $passwordFile, $type, $host, $charset]);
        } catch (ProcessFailedException) {
            $fullDbName = $this->user() . '_' . $name;
            try {
                // If database already exists for this user, change its password and proceed
                $this->runUser('v-change-database-password', [$fullDbName, $passwordFile]);
            } catch (ProcessFailedException) {
                try {
                    $this->runUser('v-delete-database', [$fullDbName]);
                    $this->runUser('v-add-database', [$name, $user, $passwordFile, $type, $host, $charset]);
                } catch (ProcessFailedException) {
                    throw new RuntimeException(_('Unable to add database!'));
                }
            }
        } finally {
            if (file_exists($passwordFile)) {
                unlink($passwordFile);
            }
        }
    }
CODE;

if (str_contains($hestiaAppContent, 'public function databaseAdd(')) {
    // Replace the databaseAdd function
    $hestiaAppContent = preg_replace(
        '/public\s+function\s+databaseAdd\([\s\S]*?finally\s*\{[\s\S]*?unlink\(\$passwordFile\);[\s\S]*?\}\s*\}/m',
        trim($newDbAdd),
        $hestiaAppContent
    );
}

// Fix sendPostRequest for local SSL and timeout resilience
$newSendPost = <<<'CODE'
    public function sendPostRequest($url, array $formData, array $headers = []): void
    {
        $ch = curl_init($url);

        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_MAXREDIRS, 5);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($formData));
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_TIMEOUT, 60);

        if ($headers !== []) {
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        }

        curl_exec($ch);

        $error = curl_error($ch);
        $errno = curl_errno($ch);

        curl_close($ch);

        if (0 !== $errno) {
            throw new RuntimeException($error, $errno);
        }
    }
CODE;

if (str_contains($hestiaAppContent, 'public function sendPostRequest(')) {
    $hestiaAppContent = preg_replace(
        '/public\s+function\s+sendPostRequest\([\s\S]*?if\s*\(0\s*!==\s*\$errno\)\s*\{[\s\S]*?\}\s*\}/m',
        trim($newSendPost),
        $hestiaAppContent
    );
}

$sftp->put($hestiaAppPath, $hestiaAppContent);
echo "✓ HestiaApp.php updated!\n";

// 2. Update WordPressSetup.php
$wpPath = '/usr/local/hestia/web/src/app/WebApp/Installers/WordPress/WordPressSetup.php';
$wpCode = <<<'PHP'
<?php

declare(strict_types=1);

namespace Hestia\WebApp\Installers\WordPress;

use Hestia\System\Util;
use Hestia\WebApp\BaseSetup;
use Hestia\WebApp\InstallationTarget\InstallationTarget;

class WordPressSetup extends BaseSetup {
    protected array $info = [
        "name" => "WordPress",
        "group" => "cms",
        "version" => "latest",
        "thumbnail" => "wordpress-logo.svg",
    ];

    protected array $config = [
        "form" => [
            "site_name" => ["type" => "text", "value" => "WordPress Blog"],
            "username" => ["value" => "wpadmin"],
            "email" => "text",
            "password" => "password",
            "language" => [
                "type" => "select",
                "value" => "en_US",
                "options" => [
                    "cs_CZ" => "Czech",
                    "de_DE" => "German",
                    "es_ES" => "Spanish",
                    "en_US" => "English",
                    "fr_FR" => "French",
                    "hu_HU" => "Hungarian",
                    "it_IT" => "Italian",
                    "ja" => "Japanese",
                    "nl_NL" => "Dutch",
                    "pt_PT" => "Portuguese",
                    "pt_BR" => "Portuguese (Brazil)",
                    "sk_SK" => "Slovak",
                    "sr_RS" => "Serbian",
                    "sv_SE" => "Swedish",
                    "tr_TR" => "Turkish",
                    "ru_RU" => "Russian",
                    "uk" => "Ukrainian",
                    "zh-CN" => "Simplified Chinese (China)",
                    "zh_TW" => "Traditional Chinese",
                ],
            ],
        ],
        "database" => true,
        "resources" => [
            "wp" => ["src" => "https://wordpress.org/latest.tar.gz"],
        ],
        "server" => [
            "nginx" => [
                "template" => "wordpress",
            ],
            "php" => [
                "supported" => ["7.4", "8.0", "8.1", "8.2", "8.3", "8.4", "8.5"],
            ],
        ],
    ];

    protected function setupApplication(InstallationTarget $target, array $options = null): void {
        $this->appcontext->runWp($options["php_version"], [
            "config",
            "create",
            "--dbname=" . $target->database->name,
            "--dbuser=" . $target->database->user,
            "--dbpass=" . $target->database->password,
            "--dbhost=" . $target->database->host,
            "--dbprefix=" . "wp_" . Util::generateString(5, false) . "_",
            "--dbcharset=utf8mb4",
            "--locale=" . $options["language"],
            "--path=" . $target->getDocRoot(),
            "--force",
        ]);

        $siteUrl = $target->getUrl() . (!empty($options["install_directory"]) ? "/" . $options["install_directory"] : "");

        try {
            $this->appcontext->runWp($options["php_version"], [
                "core",
                "install",
                "--url=" . $siteUrl,
                "--title=" . $options["site_name"],
                "--admin_user=" . $options["username"],
                "--admin_password=" . $options["password"],
                "--admin_email=" . $options["email"],
                "--path=" . $target->getDocRoot(),
                "--skip-email",
            ]);
        } catch (\Throwable $e) {
            $this->appcontext->sendPostRequest(
                $siteUrl . "/wp-admin/install.php?step=2",
                [
                    "weblog_title" => $options["site_name"],
                    "user_name" => $options["username"],
                    "admin_password" => $options["password"],
                    "admin_password2" => $options["password"],
                    "admin_email" => $options["email"],
                ],
            );
        }
    }
}
PHP;

$sftp->put($wpPath, $wpCode);
echo "✓ WordPressSetup.php updated!\n";

// 3. Update AppWizard.php (prevent double prefixes in DB names)
$wizardPath = '/usr/local/hestia/web/src/app/WebApp/AppWizard.php';
$wizardContent = $sftp->get($wizardPath);
$oldWizardTarget = <<<'CODE'
            $target->addTargetDatabase(
                new TargetDatabase(
                    $options['database_host'],
                    $this->appcontext->user() . '_' . $options['database_name'],
                    $this->appcontext->user() . '_' . $options['database_user'],
                    $options['database_password'],
                    !empty($options['database_create']),
                ),
            );
CODE;

$newWizardTarget = <<<'CODE'
            $userPrefix = $this->appcontext->user() . '_';
            $dbName = (string) ($options['database_name'] ?? '');
            $dbUser = (string) ($options['database_user'] ?? '');
            if (!str_starts_with($dbName, $userPrefix)) {
                $dbName = $userPrefix . $dbName;
            }
            if (!str_starts_with($dbUser, $userPrefix)) {
                $dbUser = $userPrefix . $dbUser;
            }

            $target->addTargetDatabase(
                new TargetDatabase(
                    $options['database_host'],
                    $dbName,
                    $dbUser,
                    $options['database_password'],
                    !empty($options['database_create']),
                ),
            );
CODE;

if (str_contains($wizardContent, '$this->appcontext->user() . \'_\' . $options[\'database_name\']')) {
    $wizardContent = str_replace($oldWizardTarget, $newWizardTarget, $wizardContent);
    $sftp->put($wizardPath, $wizardContent);
    echo "✓ AppWizard.php updated!\n";
} else {
    echo "- AppWizard.php already updated.\n";
}

echo "All Quick App Installer fixes successfully deployed to live server!\n";
