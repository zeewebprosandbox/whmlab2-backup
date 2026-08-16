<?php

namespace App\Support;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use phpseclib3\Crypt\PublicKeyLoader;
use phpseclib3\Net\SFTP;
use phpseclib3\Net\SSH2;

class ZodPanelNodeBootstrapper
{
    private array $log = [];
    private array $credentials = [];
    private ?SSH2 $ssh = null;
    private ?SFTP $sftp = null;

    public function preview(array $credentials): array
    {
        try {
            $connected = $this->connect($credentials);
            if (!$connected['success']) {
                return $connected;
            }

            $state = $this->inspect();

            return [
                'success' => true,
                'message' => $state['hestia_installed']
                    ? 'Existing Hestia/ZodPanel server detected. Custom files can be synced without reinstalling.'
                    : ($state['fresh'] ? 'Fresh VPS detected. Ready for automated ZodPanel bootstrap.' : 'Server is not fresh. Cleaning requires explicit admin confirmation.'),
                'data' => $state,
                'log' => $this->log,
            ];
        } catch (\Throwable $e) {
            $this->line('Error during preview: ' . $e->getMessage());
            return $this->fail('Preview error: ' . $e->getMessage());
        }
    }

    public function bootstrap(array $credentials, array $options = []): array
    {
        try {
            $connected = $this->connect($credentials);
            if (!$connected['success']) {
                return $connected;
            }

            $state = $this->inspect();
            if (!$state['fresh'] && !$state['hestia_installed'] && empty($options['clean'])) {
                return $this->fail('This VPS is not fresh. Tick the clean-server confirmation before installing ZodPanel.', [
                    'state' => $state,
                ]);
            }

            $token = $options['token'] ?? Str::random(64);
            $hostname = $credentials['panel_hostname'] ?: $credentials['host'];

            if (!$state['hestia_installed']) {
                if (!empty($options['clean'])) {
                    $this->cleanServer();
                }

                $this->installHestia($hostname, $credentials['admin_email'] ?? 'admin@' . $hostname);
            }

            $this->syncCustomFiles();
            $this->writeNodeEnvironment($token);
            $this->finalizeNode();

            $info = $this->inspect();

            return [
                'success' => true,
                'message' => 'ZodPanel node bootstrap completed',
                'token' => $token,
                'data' => $info,
                'log' => $this->log,
            ];
        } catch (\Throwable $e) {
            $this->line('Error during bootstrap: ' . $e->getMessage());
            return $this->fail('Bootstrap error: ' . $e->getMessage());
        }
    }

    public function syncCustomLayer(array $credentials, ?string $token = null): array
    {
        try {
            $connected = $this->connect($credentials);
            if (!$connected['success']) {
                return $connected;
            }

            $state = $this->inspect();
            if (!$state['hestia_installed']) {
                return $this->fail('Hestia is not installed on this server. Run full bootstrap first.', ['state' => $state]);
            }

            $this->syncCustomFiles();
            $this->installNodejs();

            if ($token) {
                $this->writeNodeEnvironment($token);
            }

            $this->finalizeNode();

            return [
                'success' => true,
                'message' => 'ZodPanel custom layer synced',
                'data' => $this->inspect(),
                'log' => $this->log,
            ];
        } catch (\Throwable $e) {
            $this->line('Error during custom sync: ' . $e->getMessage());
            return $this->fail('Sync error: ' . $e->getMessage());
        }
    }

    private function connect(array $credentials): array
    {
        $this->credentials = $credentials;
        $host = trim($credentials['host'] ?? '');
        $port = (int) ($credentials['ssh_port'] ?? 22);
        $username = trim($credentials['ssh_username'] ?? 'root');

        if (empty($host)) {
            return $this->fail('VPS Host / IP Address is required.');
        }

        $this->line("Initiating SSH connection to {$username}@{$host}:{$port} (timeout 4s)...");

        try {
            $this->ssh = new SSH2($host, $port, 10);
        } catch (\Throwable $e) {
            $this->line("[NET] SSH socket connection failed: " . $e->getMessage());
            return $this->fail("Cannot connect to {$host}:{$port} via SSH — check that the server is online and port {$port} is accessible. Error: " . $e->getMessage());
        }

        $auth = $credentials['ssh_private_key'] ?? null;
        if ($auth) {
            try {
                $auth = PublicKeyLoader::loadPrivateKey($auth, $credentials['ssh_private_key_passphrase'] ?? false);
            } catch (\Throwable $e) {
                return $this->fail("Invalid SSH private key: " . $e->getMessage());
            }
        } else {
            $auth = $credentials['ssh_password'] ?? '';
        }

        try {
            if (!$this->ssh->login($username, $auth)) {
                $this->line("[SSH] Authentication failed for {$username}@{$host}.");
                return $this->fail("SSH authentication failed for {$username}@{$host}:{$port} — please verify your root SSH password and ensure password authentication is enabled on this server.");
            }
        } catch (\Throwable $e) {
            $this->line("[SSH] Login error: " . $e->getMessage());
            return $this->fail("SSH login error for {$username}@{$host}:{$port}: " . $e->getMessage());
        }

        $this->line("Connected successfully to {$host}:{$port} as {$username}.");
        return ['success' => true];
    }

    private function inspect(): array
    {
        $hostname = trim($this->run('hostname -f 2>/dev/null || hostname'));
        $ip = trim($this->run("ip -4 route get 1.1.1.1 2>/dev/null | awk '{print $7; exit}'"));
        $hestia = trim($this->run('test -d /usr/local/hestia && echo yes || echo no')) === 'yes';
        $homeCount = (int) trim($this->run("find /home -mindepth 1 -maxdepth 1 -type d 2>/dev/null | wc -l"));
        $webStack = trim($this->run("command -v nginx >/dev/null 2>&1 && echo nginx; command -v apache2 >/dev/null 2>&1 && echo apache2; command -v mysql >/dev/null 2>&1 && echo mysql; command -v mariadb >/dev/null 2>&1 && echo mariadb"));
        $bridge = trim($this->run('test -f /usr/local/hestia/web/api/whmlab/index.php && echo yes || echo no')) === 'yes';

        return [
            'hostname' => $hostname,
            'ip_address' => $ip,
            'hestia_installed' => $hestia,
            'zodpanel_bridge_installed' => $bridge,
            'home_directories' => $homeCount,
            'detected_stack' => array_values(array_filter(explode("\n", $webStack))),
            'fresh' => !$hestia && $homeCount === 0 && trim($webStack) === '',
        ];
    }

    private function cleanServer(): void
    {
        $this->line('Cleaning known hosting stack packages and old Hestia paths');
        $this->run('systemctl stop hestia nginx apache2 mysql mariadb exim4 dovecot bind9 named php*-fpm 2>/dev/null || true');
        $this->run('DEBIAN_FRONTEND=noninteractive apt-get -y purge hestia* nginx* apache2* mysql-* mariadb-* exim4* dovecot* bind9* named* proftpd* vsftpd* 2>/dev/null || true');
        $this->run('DEBIAN_FRONTEND=noninteractive apt-get -y autoremove 2>/dev/null || true');
        $this->run('rm -rf /usr/local/hestia /etc/hestia /var/log/hestia /root/.hst_* 2>/dev/null || true');
    }

    private function installHestia(string $hostname, string $email): void
    {
        $url = config('zodpanel.hestia_install_url');

        $this->run('apt-get update 2>/dev/null || true');
        $this->run('DEBIAN_FRONTEND=noninteractive apt-get install -y curl wget sudo gnupg2 lsb-release ca-certificates 2>/dev/null || true');

        $this->run("curl -fsSL {$url} -o /root/hst-install.sh 2>/dev/null || wget -q {$url} -O /root/hst-install.sh");
        $this->run('chmod +x /root/hst-install.sh 2>/dev/null || true');

        $password = Str::random(24);

        $cmd = sprintf(
            'bash /root/hst-install.sh --interactive no --hostname %s --email %s --username %s --password %s --force',
            escapeshellarg($hostname),
            escapeshellarg($email),
            escapeshellarg('admin'),
            escapeshellarg($password)
        );

        $this->line($cmd);
        $output = $this->run($cmd . ' 2>&1');
        $this->line($output);

        if (!$this->isLocalFallback && $this->ssh && $this->ssh->getExitStatus() !== 0) {
            throw new \RuntimeException("Hestia installer failed.\n\n" . $output);
        }

        $this->run('/usr/local/hestia/bin/v-change-sys-config-value ENFORCE_SUBDOMAIN_OWNERSHIP no 2>/dev/null || true');
        $this->run('sed -i \'s/ENFORCE_SUBDOMAIN_OWNERSHIP=.*/ENFORCE_SUBDOMAIN_OWNERSHIP="no"/\' /usr/local/hestia/conf/hestia.conf 2>/dev/null || true');
    }

    private function syncCustomFiles(): void
    {
        $source = rtrim(config('zodpanel.custom_source_path'), '/');
        if (!is_dir($source)) {
            $this->line("Custom ZodPanel source path {$source} does not exist locally; skipping custom file upload.");
            return;
        }

        $this->line("Syncing custom ZodPanel files from {$source}");

        $host = $this->credentials['host'] ?? null;
        $password = $this->credentials['ssh_password'] ?? null;
        $port = (int) ($this->credentials['ssh_port'] ?? 22);
        $user = $this->credentials['ssh_username'] ?? 'root';

        $sshpassBin = null;
        foreach (['/usr/local/bin/sshpass', '/opt/homebrew/bin/sshpass', '/usr/bin/sshpass', 'sshpass'] as $path) {
            if (file_exists($path) || trim((string) shell_exec("PATH=\$PATH:/usr/local/bin:/opt/homebrew/bin command -v " . escapeshellarg($path) . " 2>/dev/null")) !== '') {
                $sshpassBin = $path;
                break;
            }
        }

        $githubToken = config('zodpanel.github_token');
        $repoUrl = config('zodpanel.backup_repo');

        if ($githubToken && $repoUrl && $host && $password && $sshpassBin) {
            $authRepoUrl = str_replace('https://', "https://{$githubToken}@", $repoUrl);
            if (!str_ends_with($authRepoUrl, '.git')) {
                $authRepoUrl .= '.git';
            }
            $gitCmd = sprintf(
                'PATH=$PATH:/usr/local/bin:/opt/homebrew/bin %s -p %s ssh -o StrictHostKeyChecking=no -p %d %s@%s "which git || apt-get install -y git; rm -rf /tmp/zodpanel-repo && git clone --depth 1 %s /tmp/zodpanel-repo && cp -rf /tmp/zodpanel-repo/usr/local/hestia/* /usr/local/hestia/ && rm -rf /tmp/zodpanel-repo"',
                escapeshellarg($sshpassBin),
                escapeshellarg($password),
                $port,
                escapeshellarg($user),
                escapeshellarg($host),
                escapeshellarg($authRepoUrl)
            );
            $gitOutput = (string) shell_exec($gitCmd . ' 2>&1');
            if (!str_contains($gitOutput, 'fatal:') && !str_contains($gitOutput, 'Error:')) {
                $this->line("Synced custom layer from private GitHub repository");
                $this->run('chmod +x /usr/local/hestia/bin/* 2>/dev/null || true');
                $this->run('cp /usr/local/hestia/bin/zodctl /usr/local/bin/zodctl 2>/dev/null || ln -sf /usr/local/hestia/bin/zodctl /usr/local/bin/zodctl 2>/dev/null || true');
                $this->run('chmod +x /usr/local/bin/zodctl 2>/dev/null || true');
                return;
            }
        }

        if ($host && $password && $sshpassBin) {
            $cmd = sprintf(
                'tar -cf - -C %s . | PATH=$PATH:/usr/local/bin:/opt/homebrew/bin %s -p %s ssh -o StrictHostKeyChecking=no -p %d %s@%s "mkdir -p /usr/local/hestia && tar -xf - -C /usr/local/hestia"',
                escapeshellarg($source),
                escapeshellarg($sshpassBin),
                escapeshellarg($password),
                $port,
                escapeshellarg($user),
                escapeshellarg($host)
            );
            $output = (string) shell_exec($cmd . ' 2>&1');
            $this->line("Fast tar sync completed: " . trim($output));
            $this->run('chmod +x /usr/local/hestia/bin/* 2>/dev/null || true');
            return;
        }

        if ($this->isLocalFallback) {
            $this->line("Custom ZodPanel templates and API bridge files synced cleanly.");
            return;
        }

        $sftp = $this->getSftp();
        if (!$sftp) {
            $this->line("Warning: Unable to establish SFTP fallback connection.");
            return;
        }

        foreach (File::allFiles($source) as $file) {
            $relative = $file->getRelativePathname();
            $remote = '/usr/local/hestia/' . $relative;
            $this->mkdir(dirname($remote));

            if (!$sftp->put($remote, $file->getContents())) {
                $this->line("Warning: Unable to upload {$relative}");
                continue;
            }

            if (str_starts_with($relative, 'bin/')) {
                $this->run('chmod 755 ' . escapeshellarg($remote) . ' 2>/dev/null || true');
            }
        }
    }

    private function getSftp(): ?SFTP
    {
        if ($this->sftp !== null) {
            return $this->sftp;
        }

        $host = $this->credentials['host'] ?? null;
        $port = (int) ($this->credentials['ssh_port'] ?? 22);
        $username = $this->credentials['ssh_username'] ?? 'root';
        $auth = $this->credentials['ssh_private_key'] ?? null;
        if ($auth) {
            $auth = PublicKeyLoader::loadPrivateKey($auth, $this->credentials['ssh_private_key_passphrase'] ?? false);
        } else {
            $auth = $this->credentials['ssh_password'] ?? '';
        }

        $sftp = new SFTP($host, $port, 20);
        if ($sftp->login($username, $auth)) {
            $this->sftp = $sftp;
            return $this->sftp;
        }

        return null;
    }

    private function writeNodeEnvironment(string $token): void
    {
        $this->line('Writing WHMPanel node environment file');
        $this->run('mkdir -p /usr/local/hestia/conf 2>/dev/null || true');
        $cmd = "cat << 'EOF' > /usr/local/hestia/conf/whmpanel.env\nWHMPANEL_NODE_TOKEN={$token}\nWHMPANEL_KVM_ENABLED=1\nEOF";
        $this->run($cmd);
        $this->run('chmod 600 /usr/local/hestia/conf/whmpanel.env 2>/dev/null || true');
    }

    private function finalizeNode(): void
    {
        $this->run('chown -R root:root /usr/local/hestia/bin/v-zodpanel-save-package-features /usr/local/hestia/bin/v-zodpanel-run-domain-command /usr/local/hestia/bin/zodpanel-ssl-sync /usr/local/hestia/bin/v-configure-zodpanel-ssl-automation /usr/local/hestia/bin/v-add-user-pma-temp-user 2>/dev/null || true');
        $this->run('chmod 755 /usr/local/hestia/bin/zodpanel-ssl-sync /usr/local/hestia/bin/v-configure-zodpanel-ssl-automation 2>/dev/null || true');

        // Configure 1-minute continuous Auto-SSL system cron job
        $cronContent = "* * * * * root /usr/local/hestia/bin/zodpanel-ssl-sync >/dev/null 2>&1\n";
        $this->run("echo " . escapeshellarg($cronContent) . " > /etc/cron.d/zodpanel-ssl-auto && chmod 644 /etc/cron.d/zodpanel-ssl-auto 2>/dev/null || true");

        // Execute initial SSL sync run
        $this->run('/usr/local/hestia/bin/zodpanel-ssl-sync 2>/dev/null || true');

        $this->run('systemctl restart hestia 2>/dev/null || service hestia restart 2>/dev/null || true');
    }

    private array $createdDirs = [];

    private function mkdir(string $path): void
    {
        if (isset($this->createdDirs[$path])) {
            return;
        }
        if ($this->ssh) {
            $this->run('mkdir -p ' . escapeshellarg($path) . ' 2>/dev/null || true');
        } else {
            $parts = explode('/', trim($path, '/'));
            $current = '';
            foreach ($parts as $part) {
                $current .= '/' . $part;
                if (!$this->sftp->is_dir($current)) {
                    @$this->sftp->mkdir($current);
                }
            }
        }
        $this->createdDirs[$path] = true;
    }

    private function run(string $command): string
    {
        return $this->ssh ? (string) $this->ssh->exec($command) : '';
    }

    private function runOrFail(string $command): string
    {
        $this->line('$ ' . $command);
        $output = $this->run($command . ' 2>&1; printf "\\n__EXIT_CODE__:$?"');
        [$body, $code] = array_pad(explode('__EXIT_CODE__:', $output, 2), 2, '1');
        $code = (int) trim($code);
        $body = trim($body);

        if ($body !== '') {
            $this->line($body);
        }

        if ($code !== 0) {
            throw new \RuntimeException("Command failed with exit code {$code}: {$command}");
        }

        return $body;
    }

    private function line(string $message): void
    {
        $this->log[] = '[' . now()->format('Y-m-d H:i:s') . '] ' . $message;
    }

    private function installNodejs(): void
    {
        $this->line('Installing Node.js 22 LTS and PM2 process manager...');
        $this->run('which node && node -v || (curl -fsSL https://deb.nodesource.com/setup_22.x | bash - && apt-get install -y nodejs build-essential)');
        $this->run('which pm2 || npm install -g pm2 yarn pnpm');
    }

    private function fail(string $message, array $extra = []): array
    {
        return array_merge([
            'success' => false,
            'message' => $message,
            'log' => $this->log,
        ], $extra);
    }
}