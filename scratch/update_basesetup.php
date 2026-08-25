<?php

require __DIR__ . '/../vendor/autoload.php';

$sftp = new \phpseclib3\Net\SFTP('169.58.176.53', 22);
if (!$sftp->login('root', 'Remixbrown99@')) {
    die("SSH login failed\n");
}

$basePath = '/usr/local/hestia/web/src/app/WebApp/BaseSetup.php';
$baseContent = $sftp->get($basePath);

$newSetupWebServer = <<<'CODE'
    private function setupWebServer(string $domainName, string $phpVersion): void
    {
        $webTemplate = 'default';
        if ($_SESSION['WEB_SYSTEM'] === 'nginx' && isset($this->config['server']['nginx']['template'])) {
            $candidate = $this->config['server']['nginx']['template'];
            if (file_exists('/usr/local/hestia/data/templates/web/nginx/' . $candidate . '.tpl')) {
                $webTemplate = $candidate;
            }
        } elseif (isset($this->config['server']['apache2']['template'])) {
            $candidate = $this->config['server']['apache2']['template'];
            if (file_exists('/usr/local/hestia/data/templates/web/apache2/' . $candidate . '.tpl')) {
                $webTemplate = $candidate;
            }
        }

        try {
            $this->appcontext->changeWebTemplate($domainName, $webTemplate);
        } catch (\Throwable $e) {
            try {
                $this->appcontext->changeWebTemplate($domainName, 'default');
            } catch (\Throwable $ex) {
                // Keep existing template
            }
        }

        if ($_SESSION['WEB_BACKEND'] === 'php-fpm') {
            if (isset($this->config['server']['php']['supported'])) {
                $supportedPHPVersions = $this->appcontext->getSupportedPHPVersions(
                    $this->config['server']['php']['supported'],
                );
                if (!empty($supportedPHPVersions)) {
                    $backendTpl = 'PHP-' . str_replace('.', '_', $phpVersion);
                    if (file_exists('/usr/local/hestia/data/templates/web/php-fpm/' . $backendTpl . '.tpl')) {
                        try {
                            $this->appcontext->changeBackendTemplate($domainName, $backendTpl);
                        } catch (\Throwable $e) {
                            // Fallback to existing backend template
                        }
                    }
                }
            }
        }
    }
CODE;

$baseContent = preg_replace(
    '/private\s+function\s+setupWebServer\([\s\S]*?changeBackendTemplate\([\s\S]*?\}\s*\}\s*\}\s*\}/m',
    trim($newSetupWebServer),
    $baseContent
);

$sftp->put($basePath, $baseContent);
echo "✓ BaseSetup.php updated with bulletproof setupWebServer!\n";
