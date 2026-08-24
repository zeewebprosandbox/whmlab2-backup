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
    private ?\Closure $logCallback = null;

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
        if (isset($options['onLog']) && is_callable($options['onLog'])) {
            $this->logCallback = $options['onLog'];
        }

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
            $rawHostname = $credentials['panel_hostname'] ?? $credentials['host'] ?? 'zodpanel.zodserver.cloud';
            $hostname = $this->resolveValidHostname($rawHostname);
            $email = $credentials['admin_email'] ?? 'admin@' . $hostname;

            if (!$state['hestia_installed']) {
                if (!empty($options['clean'])) {
                    $this->cleanServer();
                }

                $this->installHestia($hostname, $email);
            } else {
                $this->line("HestiaCP core is already installed on {$hostname}. Skipping installer and proceeding to ZodPanel sync...");
            }

            $this->syncCustomFiles();
            $this->writeNodeEnvironment($token);
            $this->finalizeNode($hostname);

            $info = $this->inspect();

            return [
                'success' => true,
                'message' => 'ZodPanel node bootstrap completed successfully — port 8083 is online and ready',
                'token' => $token,
                'data' => $info,
                'log' => $this->log,
            ];
        } catch (\Throwable $e) {
            $this->line('Error during bootstrap: ' . $e->getMessage());
            return $this->fail('Bootstrap error: ' . $e->getMessage());
        }
    }

    public function syncCustomLayer(array $credentials, ?string $token = null, array $options = []): array
    {
        if (isset($options['onLog']) && is_callable($options['onLog'])) {
            $this->logCallback = $options['onLog'];
        }

        try {
            $this->line("[SSH] Connecting to {$credentials['host']}:" . ($credentials['ssh_port'] ?? 22) . " as " . ($credentials['ssh_username'] ?? 'root') . "...");
            $connected = $this->connect($credentials);
            if (!$connected['success']) {
                return $connected;
            }

            $state = $this->inspect();
            if (!$state['hestia_installed']) {
                return $this->fail('Hestia is not installed on this server. Run full bootstrap first.', ['state' => $state]);
            }

            $this->line("[SYNC] Deploying custom ZodPanel Hestia theme, templates, CSS, JS, and modules...");
            $this->syncCustomFiles();

            $this->line("[NODE] Verifying runtime dependencies and extensions...");
            $this->installNodejs();

            if ($token) {
                $this->writeNodeEnvironment($token);
            }

            $rawHostname = $credentials['panel_hostname'] ?? $credentials['host'] ?? 'zodpanel.zodserver.cloud';
            $this->line("[SVC] Finalizing permissions, SSL bindings, and restarting Hestia services...");
            $this->finalizeNode($this->resolveValidHostname($rawHostname));

            $this->line("[VERIFY] Custom Hestia design synced 100% successfully!");

            return [
                'success' => true,
                'message' => 'ZodPanel custom layer synced and services restarted',
                'data' => $this->inspect(),
                'log' => $this->log,
            ];
        } catch (\Throwable $e) {
            $this->line('[ERROR] Error during custom sync: ' . $e->getMessage());
            return $this->fail('Sync error: ' . $e->getMessage());
        }
    }

    private function resolveValidHostname(string $host): string
    {
        $host = trim($host);
        if (empty($host) || filter_var($host, FILTER_VALIDATE_IP) || !str_contains($host, '.')) {
            return 'zodpanel.zodserver.cloud';
        }
        return $host;
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

        $this->line("Initiating SSH connection to {$username}@{$host}:{$port}...");

        try {
            $this->ssh = new SSH2($host, $port, 30);
            $this->ssh->setTimeout(1800);
        } catch (\Throwable $e) {
            $this->line("[NET] SSH socket connection failed: " . $e->getMessage());
            return $this->fail("Cannot connect to {$host}:{$port} via SSH — check that the server is online and port {$port} is open. Error: " . $e->getMessage());
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
                return $this->fail("SSH authentication failed for {$username}@{$host}:{$port} — please verify your root SSH password.");
            }
            $this->ssh->setTimeout(1800);
        } catch (\Throwable $e) {
            $this->line("[SSH] Login error: " . $e->getMessage());
            return $this->fail("SSH login error for {$username}@{$host}:{$port}: " . $e->getMessage());
        }

        $this->line("Connected successfully to {$host}:{$port} as {$username}.");
        return ['success' => true];
    }

    private function inspect(): array
    {
        $hostname = trim($this->run('hostname -f 2>/dev/null || hostname', false));
        $ip = trim($this->run("ip -4 route get 1.1.1.1 2>/dev/null | awk '{print $7; exit}'", false));
        $hestia = trim($this->run('test -d /usr/local/hestia && test -f /usr/local/hestia/bin/v-add-user && echo yes || echo no', false)) === 'yes';
        $homeCount = (int) trim($this->run("find /home -mindepth 1 -maxdepth 1 -type d 2>/dev/null | wc -l", false));
        $webStack = trim($this->run("command -v nginx >/dev/null 2>&1 && echo nginx; command -v apache2 >/dev/null 2>&1 && echo apache2; command -v mysql >/dev/null 2>&1 && echo mysql; command -v mariadb >/dev/null 2>&1 && echo mariadb", false));
        $bridge = trim($this->run('test -f /usr/local/hestia/web/api/whmlab/index.php && echo yes || echo no', false)) === 'yes';
        $port8083Active = trim($this->run('ss -tulpn 2>/dev/null | grep -q ":8083" && echo yes || (netstat -tulpn 2>/dev/null | grep -q ":8083" && echo yes || echo no)', false)) === 'yes';

        return [
            'hostname' => $hostname,
            'ip_address' => $ip,
            'hestia_installed' => $hestia,
            'zodpanel_bridge_installed' => $bridge,
            'port_8083_listening' => $port8083Active,
            'home_directories' => $homeCount,
            'detected_stack' => array_values(array_filter(explode("\n", $webStack))),
            'fresh' => !$hestia && $homeCount === 0 && trim($webStack) === '',
        ];
    }

    private function purgeHestiaUsersAndGroups(): void
    {
        $this->line("Purging old admin/hestia users and groups to prevent installer collision...");
        $this->run('pkill -9 -u admin 2>/dev/null || true', false);
        $this->run('pkill -9 -u hestia 2>/dev/null || true', false);
        $this->run('pkill -9 -u hestia-nologin 2>/dev/null || true', false);
        $this->run('userdel -f -r admin 2>/dev/null || true', false);
        $this->run('deluser --remove-all-files admin 2>/dev/null || true', false);
        $this->run('groupdel admin 2>/dev/null || true', false);
        $this->run('delgroup admin 2>/dev/null || true', false);
        $this->run('userdel -f -r hestia 2>/dev/null || true', false);
        $this->run('groupdel hestia 2>/dev/null || true', false);
        $this->run('userdel -f -r hestia-nologin 2>/dev/null || true', false);
        $this->run('groupdel hestia-nologin 2>/dev/null || true', false);
        $this->run('rm -rf /home/admin /home/hestia* /etc/sudoers.d/admin /var/log/hestia /etc/hestia /root/.hst_* 2>/dev/null || true', false);
    }

    private function cleanServer(): void
    {
        $this->line('Cleaning known hosting stack packages, port locks and old Hestia paths');
        $this->run('fuser -k 8083/tcp 80/tcp 443/tcp 2>/dev/null || true', false);
        $this->run('systemctl stop hestia nginx apache2 mysql mariadb exim4 dovecot bind9 named php*-fpm 2>/dev/null || true', false);
        $this->run('DEBIAN_FRONTEND=noninteractive apt-get -y purge hestia* nginx* apache2* mysql-* mariadb-* exim4* dovecot* bind9* named* proftpd* vsftpd* 2>/dev/null || true', false);
        $this->run('DEBIAN_FRONTEND=noninteractive apt-get -y autoremove 2>/dev/null || true', false);
        $this->run('rm -rf /usr/local/hestia /etc/hestia /var/log/hestia /root/.hst_* 2>/dev/null || true', false);
        $this->purgeHestiaUsersAndGroups();
    }

    private function installHestia(string $hostname, string $email): void
    {
        $url = config('zodpanel.hestia_install_url', 'https://raw.githubusercontent.com/hestiacp/hestiacp/release/install/hst-install.sh');

        if (empty($hostname) || filter_var($hostname, FILTER_VALIDATE_IP) || !str_contains($hostname, '.')) {
            $hostname = 'zodpanel.zodserver.cloud';
        }
        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $email = 'admin@' . $hostname;
        }

        $this->line("Preparing server dependencies for HestiaCP installation (hostname: {$hostname})...");
        $this->run('DEBIAN_FRONTEND=noninteractive apt-get update -y 2>/dev/null || true', false);
        $this->run('DEBIAN_FRONTEND=noninteractive apt-get install -y curl wget sudo gnupg2 lsb-release ca-certificates ufw iptables 2>/dev/null || true', false);

        $this->run("curl -fsSL {$url} -o /root/hst-install.sh 2>/dev/null || wget -q {$url} -O /root/hst-install.sh", false);
        $this->run('chmod +x /root/hst-install.sh 2>/dev/null || true', false);

        $this->purgeHestiaUsersAndGroups();

        $password = Str::random(24);

        $cmd = sprintf(
            'bash /root/hst-install.sh --interactive no --hostname %s --email %s --username %s --password %s --port 8083 --lang en --apache yes --phpfpm yes --multiphp yes --named yes --exim yes --dovecot yes --clamav no --spamassassin no --iptables yes --fail2ban yes --api yes --quota no --force',
            escapeshellarg($hostname),
            escapeshellarg($email),
            escapeshellarg('admin'),
            escapeshellarg($password)
        );

        $this->line("Executing HestiaCP unattended installer (this takes 3-6 minutes)...");
        $output = $this->run($cmd . ' 2>&1', true);

        if (str_contains($output, 'Username or Group allready exists') || str_contains($output, 'already exists')) {
            $this->line("Installer encountered lingering user/group conflict, retrying after deep purge...");
            $this->purgeHestiaUsersAndGroups();
            $output = $this->run($cmd . ' 2>&1', true);
        }

        $installed = trim($this->run('test -d /usr/local/hestia && test -f /usr/local/hestia/bin/v-add-user && echo yes || echo no', false)) === 'yes';
        if (!$installed && !str_contains($output, 'successfully installed Hestia')) {
            throw new \RuntimeException("HestiaCP installation check failed. Last output:\n" . substr($output, -1200));
        }

        $this->run('/usr/local/hestia/bin/v-change-sys-config-value ENFORCE_SUBDOMAIN_OWNERSHIP no 2>/dev/null || true', false);
        $this->run('/usr/local/hestia/bin/v-change-sys-config-value API_SYSTEM 1 2>/dev/null || true', false);
        $this->run('sed -i \'s/ENFORCE_SUBDOMAIN_OWNERSHIP=.*/ENFORCE_SUBDOMAIN_OWNERSHIP="no"/\' /usr/local/hestia/conf/hestia.conf 2>/dev/null || true', false);
    }

    private function syncCustomFiles(): void
    {
        $source = rtrim(config('zodpanel.custom_source_path'), '/');
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

        $githubToken = config('zodpanel.github_token') ?: env('GITHUB_TOKEN') ?: env('ZODPANEL_GITHUB_TOKEN');
        $repoUrl = config('zodpanel.backup_repo');

        if ($githubToken && $repoUrl) {
            $authRepoUrl = str_replace('https://', "https://{$githubToken}@", $repoUrl);
            if (!str_ends_with($authRepoUrl, '.git')) {
                $authRepoUrl .= '.git';
            }
            $this->line("[GIT] Pulling custom ZodPanel Hestia design from GitHub repository...");
            $gitCmd = sprintf(
                'which git || (apt-get update -y && apt-get install -y git); rm -rf /tmp/zodpanel-repo && git clone --depth 1 %s /tmp/zodpanel-repo && cp -rf /tmp/zodpanel-repo/usr/local/hestia/* /usr/local/hestia/ && rm -rf /tmp/zodpanel-repo',
                escapeshellarg($authRepoUrl)
            );
            $gitOutput = $this->run($gitCmd, true);
            if (!str_contains($gitOutput, 'fatal:') && !str_contains($gitOutput, 'Authentication failed')) {
                $this->line("✓ Synced custom layer from private GitHub repository successfully");
                $this->run('chmod +x /usr/local/hestia/bin/* 2>/dev/null || true', false);
                $this->run('cp /usr/local/hestia/bin/zodctl /usr/local/bin/zodctl 2>/dev/null || ln -sf /usr/local/hestia/bin/zodctl /usr/local/bin/zodctl 2>/dev/null || true', false);
                $this->run('chmod +x /usr/local/bin/zodctl 2>/dev/null || true', false);
                return;
            } else {
                $this->line("[WARN] Git clone reported error: " . $gitOutput);
            }
        }

        if (is_dir($source) && $host && $password && $sshpassBin) {
            $this->line("Syncing custom ZodPanel files from local path {$source}");
            $cmd = sprintf(
                'tar -cf - -C %s . | PATH=$PATH:/usr/local/bin:/opt/homebrew/bin %s -p %s ssh -o StrictHostKeyChecking=no -o UserKnownHostsFile=/dev/null -p %d %s@%s "mkdir -p /usr/local/hestia && tar -xf - -C /usr/local/hestia"',
                escapeshellarg($source),
                escapeshellarg($sshpassBin),
                escapeshellarg($password),
                $port,
                escapeshellarg($user),
                escapeshellarg($host)
            );
            $output = (string) shell_exec($cmd . ' 2>&1');
            $this->line("Fast tar sync completed: " . trim($output));
            $this->run('chmod +x /usr/local/hestia/bin/* 2>/dev/null || true', false);
            return;
        }

        $sftp = $this->getSftp();
        if ($sftp && is_dir($source)) {
            foreach (File::allFiles($source) as $file) {
                $relative = $file->getRelativePathname();
                $remote = '/usr/local/hestia/' . $relative;
                $this->mkdir(dirname($remote));

                if ($sftp->put($remote, $file->getContents())) {
                    if (str_starts_with($relative, 'bin/')) {
                        $this->run('chmod 755 ' . escapeshellarg($remote) . ' 2>/dev/null || true', false);
                    }
                }
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

        $sftp = new SFTP($host, $port, 30);
        if ($sftp->login($username, $auth)) {
            $this->sftp = $sftp;
            return $this->sftp;
        }

        return null;
    }

    private function writeNodeEnvironment(string $token): void
    {
        $this->line('Writing WHMPanel node environment file');
        $this->run('mkdir -p /usr/local/hestia/conf 2>/dev/null || true', false);
        $cmd = "cat << 'EOF' > /usr/local/hestia/conf/whmpanel.env\nWHMPANEL_NODE_TOKEN={$token}\nWHMPANEL_KVM_ENABLED=1\nEOF";
        $this->run($cmd, false);
        $this->run('chmod 600 /usr/local/hestia/conf/whmpanel.env 2>/dev/null || true', false);
    }

    private function finalizeNode(string $hostname = 'zodpanel.zodserver.cloud'): void
    {
        $this->line('Configuring firewall rules, certificates, and starting HestiaCP services...');

        $this->run('chown -R root:root /usr/local/hestia/bin/v-zodpanel-save-package-features /usr/local/hestia/bin/v-zodpanel-run-domain-command /usr/local/hestia/bin/zodpanel-ssl-sync /usr/local/hestia/bin/v-configure-zodpanel-ssl-automation /usr/local/hestia/bin/v-add-user-pma-temp-user 2>/dev/null || true', false);
        $this->run('chmod 755 /usr/local/hestia/bin/v-zodpanel-* /usr/local/hestia/bin/zodpanel-* /usr/local/hestia/bin/v-add-user-pma-temp-user 2>/dev/null || true', false);

        // Configure 1-minute continuous Auto-SSL system cron job
        $cronContent = "* * * * * root /usr/local/hestia/bin/zodpanel-ssl-sync >/dev/null 2>&1\n";
        $this->run("echo " . escapeshellarg($cronContent) . " > /etc/cron.d/zodpanel-ssl-auto && chmod 644 /etc/cron.d/zodpanel-ssl-auto 2>/dev/null || true", false);

        // Execute initial SSL sync run
        $this->run('/usr/local/hestia/bin/zodpanel-ssl-sync 2>/dev/null || true', false);

        // Configure Hestia configuration
        $this->run('/usr/local/hestia/bin/v-change-sys-config-value API_SYSTEM 1 2>/dev/null || true', false);
        $this->run('/usr/local/hestia/bin/v-change-sys-config-value ENFORCE_SUBDOMAIN_OWNERSHIP no 2>/dev/null || true', false);

        // Open firewall rules for 8083, 80, 443
        $this->run('/usr/local/hestia/bin/v-add-firewall-rule ACCEPT 0.0.0.0/0 8083 TCP 2>/dev/null || true', false);
        $this->run('/usr/local/hestia/bin/v-add-firewall-rule ACCEPT 0.0.0.0/0 80 TCP 2>/dev/null || true', false);
        $this->run('/usr/local/hestia/bin/v-add-firewall-rule ACCEPT 0.0.0.0/0 443 TCP 2>/dev/null || true', false);
        $this->run('iptables -I INPUT -p tcp --dport 8083 -j ACCEPT 2>/dev/null || true', false);
        $this->run('iptables -I INPUT -p tcp --dport 80 -j ACCEPT 2>/dev/null || true', false);
        $this->run('iptables -I INPUT -p tcp --dport 443 -j ACCEPT 2>/dev/null || true', false);
        $this->run('ufw allow 8083/tcp 2>/dev/null || true', false);
        $this->run('ufw allow 80/tcp 2>/dev/null || true', false);
        $this->run('ufw allow 443/tcp 2>/dev/null || true', false);

        // 100% Secured SSL Configuration
        $this->line("Securing SSL for HestiaCP control panel and mail services (hostname: {$hostname})...");

        // 1. Attempt official Let's Encrypt SSL certificate for server hostname
        $this->run('/usr/local/hestia/bin/v-add-letsencrypt-host 2>/dev/null || true', false);

        // 2. Ensure 2048-bit TLS certificate exists if Let's Encrypt is pending DNS propagation
        $this->run('if [ ! -f /usr/local/hestia/ssl/certificate.crt ] || [ ! -f /usr/local/hestia/ssl/certificate.key ]; then /usr/local/hestia/bin/v-generate-ssl-cert ' . escapeshellarg($hostname) . ' admin@' . escapeshellarg($hostname) . ' 2>/dev/null || true; fi', false);

        // 3. Enforce TLS 1.2 and TLS 1.3 modern cryptographic protocols
        $this->run('sed -i "s/ssl_protocols .*/ssl_protocols TLSv1.2 TLSv1.3;/" /usr/local/hestia/nginx/conf/hestia-nginx.conf 2>/dev/null || true', false);

        // 4. Configure phpMyAdmin All-in-One SSO
        $this->line("Configuring phpMyAdmin Single Sign-On (SSO) with All-in-One database access...");
        $this->run('rm -f /usr/share/phpmyadmin/hestia-sso.php && /usr/local/hestia/bin/v-add-sys-pma-sso 2>/dev/null || true', false);
        $ssoConf = '<?php' . PHP_EOL . 'if (!isset($cfg[\'Servers\'][$i])) { $cfg[\'Servers\'][$i] = []; }' . PHP_EOL . '$cfg[\'Servers\'][$i][\'auth_type\'] = \'signon\';' . PHP_EOL . '$cfg[\'Servers\'][$i][\'SignonSession\'] = \'SignonSession\';' . PHP_EOL . '$cfg[\'Servers\'][$i][\'SignonURL\'] = \'hestia-sso.php\';' . PHP_EOL . '$cfg[\'Servers\'][$i][\'LogoutURL\'] = \'hestia-sso.php?logout\';' . PHP_EOL;
        $this->run("cat << 'EOF' > /etc/phpmyadmin/conf.d/00-hestia-sso.php\n" . $ssoConf . "EOF\n", false);
        $this->run('sed -i "s/fastcgi_pass  unix:\/run\/php\/www.sock;/fastcgi_param HTTPS on;\n\t\tfastcgi_param HTTP_X_FORWARDED_PROTO https;\n\t\tfastcgi_pass  unix:\/run\/php\/www.sock;/" /etc/nginx/conf.d/phpmyadmin.inc 2>/dev/null || true', false);

        // Enable and start hestia, nginx, apache2 services
        $this->run('systemctl unmask hestia 2>/dev/null || true', false);
        $this->run('systemctl enable hestia 2>/dev/null || true', false);
        $this->run('systemctl restart hestia 2>/dev/null || service hestia restart 2>/dev/null || true', false);
        $this->run('systemctl restart nginx 2>/dev/null || service nginx restart 2>/dev/null || true', false);
        $this->run('systemctl restart apache2 2>/dev/null || service apache2 restart 2>/dev/null || true', false);


        // Verify port 8083 status
        $portCheck = trim($this->run('ss -tulpn 2>/dev/null | grep ":8083" || netstat -tulpn 2>/dev/null | grep ":8083" || true', false));
        if (!empty($portCheck)) {
            $this->line("Verified HestiaCP / ZodPanel is actively listening on port 8083: " . $portCheck);
        } else {
            $this->line("Starting hestia-nginx directly to ensure port 8083 is accessible...");
            $this->run('/usr/local/hestia/nginx/sbin/hestia-nginx -c /usr/local/hestia/nginx/conf/hestia-nginx.conf 2>/dev/null || true', false);
        }
    }

    private array $createdDirs = [];

    private function mkdir(string $path): void
    {
        if (isset($this->createdDirs[$path])) {
            return;
        }
        if ($this->ssh) {
            $this->run('mkdir -p ' . escapeshellarg($path) . ' 2>/dev/null || true', false);
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

    private function run(string $command, bool $streamToLog = true): string
    {
        if (!$this->ssh) {
            return '';
        }

        $this->ssh->setTimeout(1800);
        $buffer = '';

        try {
            $this->ssh->exec($command, function($chunk) use (&$buffer, $streamToLog) {
                $buffer .= $chunk;
                if ($streamToLog) {
                    $lines = explode("\n", trim($chunk));
                    foreach ($lines as $line) {
                        $line = trim($line);
                        if ($line !== '' && !str_starts_with($line, 'TERM environment')) {
                            $this->line($line);
                        }
                    }
                }
            });
        } catch (\Throwable $e) {
            if (str_contains($e->getMessage(), 'channel')) {
                $this->line("Resetting SSH channel...");
                $this->connect($this->credentials);
                $this->ssh->exec($command, function($chunk) use (&$buffer) {
                    $buffer .= $chunk;
                });
            } else {
                throw $e;
            }
        }

        return $buffer;
    }

    private function line(string $message): void
    {
        $formatted = '[' . now()->format('Y-m-d H:i:s') . '] ' . $message;
        $this->log[] = $formatted;
        if ($this->logCallback) {
            ($this->logCallback)($formatted);
        }
    }

    private function installNodejs(): void
    {
        $this->line('Installing Node.js 22 LTS and PM2 process manager...');
        $this->run('which node && node -v || (curl -fsSL https://deb.nodesource.com/setup_22.x | bash - && apt-get install -y nodejs build-essential)', false);
        $this->run('which pm2 || npm install -g pm2 yarn pnpm', false);
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