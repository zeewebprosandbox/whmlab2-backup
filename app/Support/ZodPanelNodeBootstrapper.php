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
    private ?SSH2 $ssh = null;
    private ?SFTP $sftp = null;

    public function preview(array $credentials): array
    {
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
    }

    public function bootstrap(array $credentials, array $options = []): array
    {
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

            $this->installHestia($hostname, $credentials['admin_email'] ?? 'admin@'.$hostname);
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
    }

    public function syncCustomLayer(array $credentials, ?string $token = null): array
    {
        $connected = $this->connect($credentials);
        if (!$connected['success']) {
            return $connected;
        }

        $state = $this->inspect();
        if (!$state['hestia_installed']) {
            return $this->fail('Hestia is not installed on this server. Run full bootstrap first.', ['state' => $state]);
        }

        $this->syncCustomFiles();

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
    }

    private function connect(array $credentials): array
    {
        $host = $credentials['host'];
        $port = (int) ($credentials['ssh_port'] ?? 22);
        $username = $credentials['ssh_username'] ?? 'root';

        $this->ssh = new SSH2($host, $port, 20);
        $this->sftp = new SFTP($host, $port, 20);

        $auth = $credentials['ssh_private_key'] ?? null;
        if ($auth) {
            $auth = PublicKeyLoader::loadPrivateKey($auth, $credentials['ssh_private_key_passphrase'] ?? false);
        } else {
            $auth = $credentials['ssh_password'] ?? '';
        }

        if (!$this->ssh->login($username, $auth) || !$this->sftp->login($username, $auth)) {
            return $this->fail('SSH login failed. Check VPS host, port, username, and password/key.');
        }

        $this->line("Connected to {$host}:{$port} as {$username}");
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
        $this->runOrFail('systemctl stop hestia nginx apache2 mysql mariadb exim4 dovecot bind9 named php*-fpm 2>/dev/null || true');
        $this->runOrFail('DEBIAN_FRONTEND=noninteractive apt-get -y purge hestia* nginx* apache2* mysql-* mariadb-* exim4* dovecot* bind9* named* proftpd* vsftpd* 2>/dev/null || true');
        $this->runOrFail('DEBIAN_FRONTEND=noninteractive apt-get -y autoremove 2>/dev/null || true');
        $this->runOrFail('rm -rf /usr/local/hestia /etc/hestia /var/log/hestia /root/.hst_*');
    }

    private function installHestia(string $hostname, string $email): void
    {
        $url = config('zodpanel.hestia_install_url');
        $this->line("Installing Hestia from {$url}");
        $this->runOrFail('apt-get update -y');
        $this->runOrFail('apt-get install -y curl ca-certificates sudo gnupg lsb-release');
        $this->runOrFail('curl -fsSL '.escapeshellarg($url).' -o /root/hst-install.sh');
        $this->runOrFail('bash /root/hst-install.sh --interactive no --hostname '.escapeshellarg($hostname).' --email '.escapeshellarg($email).' --password '.escapeshellarg(Str::random(24)).' --force');
    }

    private function syncCustomFiles(): void
    {
        $source = rtrim(config('zodpanel.custom_source_path'), '/');
        if (!is_dir($source)) {
            throw new \RuntimeException("ZodPanel custom source path does not exist: {$source}");
        }

        $this->line("Syncing custom ZodPanel files from {$source}");
        foreach (File::allFiles($source) as $file) {
            $relative = $file->getRelativePathname();
            $remote = '/usr/local/hestia/'.$relative;
            $this->mkdir(dirname($remote));

            if (!$this->sftp->put($remote, $file->getContents())) {
                throw new \RuntimeException("Unable to upload {$relative}");
            }

            if (str_starts_with($relative, 'bin/')) {
                $this->runOrFail('chmod 755 '.escapeshellarg($remote));
            }
        }
    }

    private function writeNodeEnvironment(string $token): void
    {
        $content = "WHMPANEL_NODE_TOKEN=".escapeshellarg($token)."\nWHMPANEL_KVM_ENABLED=1\n";
        $this->line('Writing WHMPanel node environment');
        $this->sftp->put('/usr/local/hestia/conf/whmpanel.env', $content);
        $this->runOrFail('chmod 600 /usr/local/hestia/conf/whmpanel.env');
    }

    private function finalizeNode(): void
    {
        $this->runOrFail('chown -R root:root /usr/local/hestia/bin/v-zodpanel-save-package-features /usr/local/hestia/bin/v-zodpanel-run-domain-command /usr/local/hestia/bin/zodpanel-ssl-sync /usr/local/hestia/bin/v-add-user-pma-temp-user 2>/dev/null || true');
        $this->runOrFail('systemctl restart hestia 2>/dev/null || service hestia restart 2>/dev/null || true');
    }

    private function mkdir(string $path): void
    {
        $parts = explode('/', trim($path, '/'));
        $current = '';
        foreach ($parts as $part) {
            $current .= '/'.$part;
            if (!$this->sftp->is_dir($current)) {
                $this->sftp->mkdir($current);
            }
        }
    }

    private function run(string $command): string
    {
        return (string) $this->ssh->exec($command);
    }

    private function runOrFail(string $command): string
    {
        $this->line('$ '.$command);
        $output = $this->run($command.' 2>&1; printf "\\n__EXIT_CODE__:$?"');
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
        $this->log[] = '['.now()->format('Y-m-d H:i:s').'] '.$message;
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
