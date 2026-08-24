<?php

namespace App\HostingModule\Server;

use App\Models\WhmPanelAccount;
use App\Models\WhmPanelDnsRecord;
use App\Models\WhmPanelNode;
use App\Models\WhmPanelSsoToken;
use App\Models\WhmPanelUsageStat;
use App\Models\WhmPanelWebsite;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class Whmpanel implements HostingManagerInterface
{
    public function create($hosting)
    {
        if ($this->usesBridge($hosting->server)) {
            $username = $hosting->username ?: $this->usernameFor($hosting);
            $password = $hosting->password ?: Str::password(18);
            $domain = $hosting->domain ?: $username . '.local';
            $package = $hosting->product->package_name ?: 'default';

            $response = $this->bridgeRequest($hosting->server, 'post', 'users', [
                'username' => $username,
                'password' => $password,
                'email' => $hosting->user->email,
                'package' => $package,
                'domain' => $domain,
            ]);

            if (!$response['success']) {
                return $response;
            }

            $hosting->username = $username;
            $hosting->password = $password;
            $hosting->package_name = $package;
            $hosting->dedicated_ip = $hosting->server->ip_address;
            $hosting->ip = $hosting->server->ip_address;
            $hosting->ns1 = $hosting->server->ns1;
            $hosting->ns2 = $hosting->server->ns2;
            $hosting->save();

            $this->mirrorBridgeAccount($hosting, $response['data'] ?? []);

            return [
                'success' => true,
                'message' => 'WHMPanel account provisioned on Hestia-derived node',
                'data' => $response['data'] ?? null,
            ];
        }

        $node = $this->nodeForServer($hosting->server);
        $username = $hosting->username ?: $this->usernameFor($hosting);
        $domain = $hosting->domain ?: $username . '.local';

        $account = WhmPanelAccount::firstOrNew([
            'node_id' => $node->id,
            'username' => $username,
        ]);

        $account->hosting_id = $hosting->id;
        $account->user_id = $hosting->user_id;
        $account->email = $hosting->user->email;
        $account->package = $hosting->product->package_name ?: $hosting->product->name;
        $account->primary_domain = $domain;
        $account->status = 'active';
        $account->disk_limit_mb = $this->packageDiskLimit($hosting);
        $account->bandwidth_limit_mb = $this->packageBandwidthLimit($hosting);
        $account->save();

        $website = WhmPanelWebsite::firstOrCreate(
            ['account_id' => $account->id, 'domain' => $domain],
            [
                'document_root' => "/home/$username/web/$domain/public_html",
                'php_version' => '8.3',
                'ssl_enabled' => true,
                'status' => 'active',
            ]
        );

        $this->ensureDefaultDns($website, $hosting->server->ip_address ?: '127.0.0.1');
        $this->recordUsage($node, $account);

        $hosting->username = $username;
        $hosting->package_name = $account->package;
        $hosting->dedicated_ip = $hosting->server->ip_address;
        $hosting->ip = $hosting->server->ip_address;
        $hosting->ns1 = $hosting->server->ns1;
        $hosting->ns2 = $hosting->server->ns2;
        $hosting->save();

        return [
            'success' => true,
            'message' => 'WHMPanel account provisioned locally',
            'data' => $account->load('websites.dnsRecords'),
        ];
    }

    public function suspend($data)
    {
        $hosting = $data['hosting'];
        if ($this->usesBridge($hosting->server)) {
            $response = $this->bridgeRequest($hosting->server, 'post', 'users/' . $hosting->username . '/suspend');
            if (!$response['success']) {
                return $response;
            }
        }

        $account = $this->accountForHosting($hosting);
        $account->status = 'suspended';
        $account->suspended_at = now();
        $account->save();
        $account->websites()->update(['status' => 'suspended']);

        return ['success' => true, 'message' => 'WHMPanel account suspended'];
    }

    public function unSuspend($hosting)
    {
        if ($this->usesBridge($hosting->server)) {
            $response = $this->bridgeRequest($hosting->server, 'post', 'users/' . $hosting->username . '/unsuspend');
            if (!$response['success']) {
                return $response;
            }
        }

        $account = $this->accountForHosting($hosting);
        $account->status = 'active';
        $account->suspended_at = null;
        $account->save();
        $account->websites()->update(['status' => 'active']);

        return ['success' => true, 'message' => 'WHMPanel account unsuspended'];
    }

    public function terminate($hosting)
    {
        $account = $this->accountForHosting($hosting);
        $account->status = 'terminated';
        $account->terminated_at = now();
        $account->save();
        $account->websites()->update(['status' => 'terminated']);

        return ['success' => true, 'message' => 'WHMPanel account terminated with local retention'];
    }

    public function changePackage($hosting)
    {
        $account = $this->accountForHosting($hosting);
        $account->package = $hosting->product->package_name ?: $hosting->product->name;
        $account->disk_limit_mb = $this->packageDiskLimit($hosting);
        $account->bandwidth_limit_mb = $this->packageBandwidthLimit($hosting);
        $account->save();

        return ['success' => true, 'message' => 'WHMPanel package changed'];
    }

    public function changePassword($hosting)
    {
        return ['success' => true, 'message' => 'WHMPanel password rotated in billing record'];
    }

    public function syncConfigOptions($hosting)
    {
        $limits = $this->configLimitsForHosting($hosting);

        if ($this->usesBridge($hosting->server)) {
            $response = $this->bridgeRequest($hosting->server, 'put', 'users/' . $hosting->username, [
                'package' => $hosting->product->package_name ?: $hosting->product->name,
                'disk_limit_mb' => $limits['disk_limit_mb'],
                'bandwidth_limit_mb' => $limits['bandwidth_limit_mb'],
            ]);

            if (!$response['success']) {
                return $response;
            }
        }

        $account = $this->accountForHosting($hosting);

        $account->disk_limit_mb = $limits['disk_limit_mb'];
        $account->bandwidth_limit_mb = $limits['bandwidth_limit_mb'];
        $account->save();

        return ['success' => true, 'message' => 'WHMPanel configurable options synced'];
    }

    public function accountSummary($hosting)
    {
        if ($this->usesBridge($hosting->server) && $hosting->username) {
            $response = $this->bridgeRequest($hosting->server, 'get', 'users/' . $hosting->username);
            if ($response['success']) {
                return [
                    'success' => true,
                    'processed_data' => [
                        'status' => data_get($response, 'data.' . $hosting->username . '.SUSPENDED') === 'yes' ? 'suspended' : 'active',
                        'disk_usage' => data_get($response, 'data.' . $hosting->username . '.U_DISK', '0'),
                        'disk_limit' => data_get($response, 'data.' . $hosting->username . '.DISK_QUOTA', 'default'),
                        'bandwidth_usage' => data_get($response, 'data.' . $hosting->username . '.U_BANDWIDTH', '0'),
                        'websites' => data_get($response, 'data.' . $hosting->username . '.U_WEB_DOMAINS', '0'),
                    ],
                    'raw_data' => $response['data'],
                ];
            }
        }

        $account = WhmPanelAccount::where('hosting_id', $hosting->id)->with('websites')->first();

        if (!$account) {
            return [
                'success' => false,
                'message' => 'WHMPanel account has not been provisioned yet',
                'processed_data' => null,
                'raw_data' => null,
            ];
        }

        $diskPercent = $account->disk_limit_mb ? round(($account->disk_used_mb / $account->disk_limit_mb) * 100, 2) : 0;
        $bandwidthPercent = $account->bandwidth_limit_mb ? round(($account->bandwidth_used_mb / $account->bandwidth_limit_mb) * 100, 2) : 0;

        return [
            'success' => true,
            'processed_data' => [
                'disk_usage' => $account->disk_used_mb . ' MB',
                'disk_limit' => $account->disk_limit_mb . ' MB',
                'disk_usage_percent' => $diskPercent,
                'bandwidth_usage' => $account->bandwidth_used_mb . ' MB',
                'bandwidth_limit' => $account->bandwidth_limit_mb . ' MB',
                'bandwidth_usage_percent' => $bandwidthPercent,
                'cpu_percent' => $account->cpu_percent,
                'memory_percent' => $account->memory_percent,
                'websites' => $account->websites->count(),
                'status' => $account->status,
            ],
            'raw_data' => $account,
        ];
    }

    public function loginServer($server)
    {
        if ($this->usesBridge($server)) {
            $response = $this->bridgeRequest($server, 'get', 'server/info');
            if (!$response['success']) {
                return $response;
            }

            return [
                'success' => true,
                'url' => $server->hostname,
                'message' => 'Connected to WHMPanel Hestia-derived node',
            ];
        }

        $node = $this->nodeForServer($server);

        return [
            'success' => true,
            'url' => route('whmpanel.dashboard'),
            'message' => "Connected to WHMPanel node {$node->name}",
        ];
    }

    public function loginAccount($hosting)
    {
        $account = $this->accountForHosting($hosting);
        $plainToken = Str::random(48);

        $token = new WhmPanelSsoToken();
        $token->account_id = $account->id;
        $token->token_hash = Hash::make($plainToken);
        $token->expires_at = now()->addMinutes(15);
        $token->save();

        return [
            'success' => true,
            'url' => route('whmpanel.sso', ['token' => $plainToken]),
        ];
    }

    public function getIP($server)
    {
        if ($this->usesBridge($server)) {
            $host = parse_url($server->hostname, PHP_URL_HOST);
            return gethostbyname($host ?: $server->host ?: '127.0.0.1');
        }

        return $server->ip_address ?: '127.0.0.1';
    }

    public function getPackage($serverGroup)
    {
        $packages = [];

        foreach ($serverGroup->servers()->provisionable()->get() as $server) {
            if ($this->usesBridge($server)) {
                $response = $this->bridgeRequest($server, 'get', 'server/info');
                if (!$response['success']) {
                    return $response;
                }
            }

            $packages[$server->id] = ['starter', 'business', 'professional', 'enterprise'];
        }

        return [
            'success' => true,
            'data' => $packages,
        ];
    }

    public function createPackage($data): array
    {
        $serverGroup = $data['server_group'];
        $products = $data['products'];
        $server = $serverGroup->servers()->provisionable()->first() ?: $serverGroup->servers()->active()->first();
        $synced = [];

        if (!$server) {
            return [
                'success' => false,
                'message' => 'The selected WHMPanel server group has no active server',
            ];
        }

        foreach ($products as $product) {
            $synced[$product->id] = [
                'server_id' => $server->id,
                'package_name' => $product->package_name ?: str($product->name)->slug()->toString(),
                'status' => 'existing',
            ];
        }

        return [
            'success' => true,
            'data' => $synced,
        ];
    }

    private function nodeForServer($server): WhmPanelNode
    {
        return WhmPanelNode::firstOrCreate(
            ['server_id' => $server->id],
            [
                'name' => $server->name ?: 'Local WHMPanel',
                'hostname' => $server->hostname ?: config('app.url'),
                'ip_address' => $server->ip_address ?: '127.0.0.1',
                'api_token' => $server->api_token ?: Str::random(48),
                'status' => 'online',
                'last_sync_at' => now(),
            ]
        );
    }

    private function accountForHosting($hosting): WhmPanelAccount
    {
        $account = WhmPanelAccount::where('hosting_id', $hosting->id)->first();

        if ($account) {
            return $account;
        }

        $this->create($hosting);
        return WhmPanelAccount::where('hosting_id', $hosting->id)->firstOrFail();
    }

    private function usernameFor($hosting): string
    {
        $base = strtolower($hosting->domain ?: $hosting->user->username ?: 'whmpanel');
        $base = preg_replace('/[^a-z]/', '', $base);
        $base = substr($base ?: 'whmpanel', 0, 10);

        if (strlen($base) < 3) {
            $base = str_pad($base, 3, 'x');
        }

        return WhmPanelAccount::where('username', $base)->exists() ? $base . $hosting->id : $base;
    }

    private function packageDiskLimit($hosting): int
    {
        return str_contains(strtolower($hosting->product->name), 'dedicated') ? 102400 : 10240;
    }

    private function packageBandwidthLimit($hosting): int
    {
        return str_contains(strtolower($hosting->product->name), 'dedicated') ? 1024000 : 102400;
    }

    private function configLimitsForHosting($hosting): array
    {
        $text = strtolower((string) @$hosting->product->description);
        $hosting->loadMissing('hostingConfigs.select', 'hostingConfigs.option');

        foreach ($hosting->hostingConfigs as $config) {
            $text .= "\n" . @$config->select->name . ' ' . @$config->option->name;
        }

        return [
            'disk_limit_mb' => $this->limitFromText($text, 'storage', $this->packageDiskLimit($hosting)),
            'bandwidth_limit_mb' => $this->limitFromText($text, 'bandwidth', $this->packageBandwidthLimit($hosting)),
        ];
    }

    private function limitFromText(string $text, string $kind, int $fallback): int
    {
        if ($kind === 'storage' && preg_match('/unlimited\s+(?:ssd\s+|nvme\s+)?(?:storage|disk|disk\s+space|web\s+space)/', $text)) {
            return 1048576;
        }

        if ($kind === 'bandwidth' && (
            str_contains($text, 'unlimited bandwidth') ||
            str_contains($text, 'unlimited transfer') ||
            str_contains($text, 'unlimited traffic')
        )) {
            return 10485760;
        }

        $patterns = $kind === 'storage'
            ? ['/(\\d+(?:\\.\\d+)?)\\s*(tb|gb|mb)\\s*(?:ssd\\s+|nvme\\s+)?(?:storage|disk|disk\\s+space|web\\s+space)/']
            : ['/(\\d+(?:\\.\\d+)?)\\s*(tb|gb|mb)\\s*(?:bandwidth|transfer|traffic)/'];

        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $text, $match)) {
                return match (strtolower($match[2])) {
                    'tb' => (int) round((float) $match[1] * 1024 * 1024),
                    'gb' => (int) round((float) $match[1] * 1024),
                    default => (int) round((float) $match[1]),
                };
            }
        }

        return $fallback;
    }

    private function ensureDefaultDns(WhmPanelWebsite $website, string $ip): void
    {
        WhmPanelDnsRecord::firstOrCreate(
            ['website_id' => $website->id, 'name' => '@', 'type' => 'A'],
            ['value' => $ip, 'ttl' => 3600]
        );

        WhmPanelDnsRecord::firstOrCreate(
            ['website_id' => $website->id, 'name' => 'www', 'type' => 'CNAME'],
            ['value' => $website->domain, 'ttl' => 3600]
        );
    }

    private function recordUsage(WhmPanelNode $node, WhmPanelAccount $account): void
    {
        WhmPanelUsageStat::create([
            'node_id' => $node->id,
            'account_id' => $account->id,
            'disk_used_mb' => $account->disk_used_mb,
            'bandwidth_used_mb' => $account->bandwidth_used_mb,
            'cpu_percent' => $account->cpu_percent,
            'memory_percent' => $account->memory_percent,
            'recorded_at' => now(),
        ]);
    }

    private function usesBridge($server): bool
    {
        return $server && $server->hostname && ($server->api_token || $server->security_token);
    }

    private function bridgeRequest($server, string $method, string $endpoint, array $payload = []): array
    {
        $token = $server->api_token ?: $server->security_token;
        $url = rtrim($server->hostname, '/') . '/api/whmlab/index.php';

        try {
            $client = Http::acceptJson()
                ->withoutVerifying()
                ->timeout(20)
                ->withToken($token);

            $response = strtolower($method) === 'get'
                ? $client->get($url, ['endpoint' => $endpoint])
                : $client->asJson()->post($url . '?endpoint=' . urlencode($endpoint), $payload);

            if (!$response->successful()) {
                return [
                    'success' => false,
                    'message' => data_get($response->json(), 'error.message') ?: $response->body() ?: 'WHMPanel node request failed',
                ];
            }

            $body = $response->json();

            return [
                'success' => (bool) data_get($body, 'success', true),
                'message' => data_get($body, 'error.message', 'OK'),
                'data' => data_get($body, 'data', $body),
            ];
        } catch (\Throwable $exception) {
            return [
                'success' => false,
                'message' => 'WHMPanel node connection failed: ' . $exception->getMessage(),
            ];
        }
    }

    private function mirrorBridgeAccount($hosting, array $data): void
    {
        $node = $this->nodeForServer($hosting->server);
        $account = WhmPanelAccount::firstOrNew([
            'node_id' => $node->id,
            'username' => $hosting->username,
        ]);

        $account->hosting_id = $hosting->id;
        $account->user_id = $hosting->user_id;
        $account->email = $hosting->user->email;
        $account->package = $hosting->package_name ?: data_get($data, 'package', 'default');
        $account->primary_domain = $hosting->domain;
        $account->status = 'active';
        $account->save();

        if ($hosting->domain) {
            $website = WhmPanelWebsite::firstOrCreate(
                ['account_id' => $account->id, 'domain' => $hosting->domain],
                [
                    'document_root' => "/home/{$hosting->username}/web/{$hosting->domain}/public_html",
                    'php_version' => '8.3',
                    'ssl_enabled' => true,
                    'status' => 'active',
                ]
            );

            $this->ensureDefaultDns($website, $hosting->server->ip_address ?: '127.0.0.1');
        }
    }
}
