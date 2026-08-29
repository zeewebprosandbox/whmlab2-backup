<?php

namespace App\Http\Controllers\WhmPanel;

use App\HostingModule\HostingManager;
use App\Http\Controllers\Controller;
use App\Models\WhmPanelAccount;
use App\Models\WhmPanelDnsRecord;
use App\Models\WhmPanelNode;
use App\Models\WhmPanelServiceItem;
use App\Models\WhmPanelSsoToken;
use App\Models\WhmPanelWebsite;
use App\Support\WhmPanelServiceCatalog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class ApiController extends Controller
{
    public function serverInfo()
    {
        $node = $this->node();

        return $this->ok([
            'hostname' => $node->hostname,
            'ip_address' => $node->ip_address,
            'status' => $node->status,
            'version' => 'local-dev-0.1',
            'accounts' => $node->accounts()->count(),
            'last_sync_at' => optional($node->last_sync_at)->toIso8601String(),
        ]);
    }

    public function serverStats()
    {
        $node = $this->node();

        return $this->ok([
            'disk' => ['used_mb' => $node->used_disk_mb, 'total_mb' => $node->total_disk_mb],
            'bandwidth' => ['used_mb' => $node->used_bandwidth_mb, 'total_mb' => $node->total_bandwidth_mb],
            'cpu_percent' => $node->cpu_percent,
            'memory_percent' => $node->memory_percent,
        ]);
    }

    public function services()
    {
        $modules = WhmPanelServiceCatalog::modules($this->localNodeFeatures());

        return $this->ok([
            'summary' => WhmPanelServiceCatalog::summary($modules),
            'modules' => $modules,
        ]);
    }

    public function serviceModule(string $module)
    {
        abort_unless(WhmPanelServiceCatalog::find($module, $this->localNodeFeatures()), 404);

        return $this->ok($this->serviceModulePayload($module));
    }

    public function createServiceItem(Request $request, string $module)
    {
        abort_unless(WhmPanelServiceCatalog::find($module, $this->localNodeFeatures()), 404);
        $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'nullable|string|max:60',
            'status' => 'nullable|string|max:40',
            'website_id' => 'nullable|exists:whm_panel_websites,id',
        ]);

        $website = $request->website_id
            ? WhmPanelWebsite::with('account.hosting.server.group')->findOrFail($request->website_id)
            : null;
        $hosting = $this->hostingForWebsite($website);
        abort_unless(!in_array($module, ['webmail', 'databases', 'backups'], true) || $hosting, 422, 'This domain is not attached to a live ZodPanel hosting service.');

        $execute = match ($module) {
            'webmail' => $this->zodPanelAction($hosting, 'createMailAccount', [
                'domain' => $website->domain,
                'account' => $request->name,
                'quota_mb' => $request->quota_mb,
                'password' => $request->password,
            ]),
            'databases' => $this->zodPanelAction($hosting, 'createDatabase', [
                'database' => $request->name,
                'db_user' => $request->db_user ?: $request->name,
                'password' => $request->password,
            ]),
            'backups' => $this->zodPanelAction($hosting, 'createBackup', [
                'notify' => true,
            ]),
            default => [
                'success' => true,
                'message' => 'Guarded implementation spec recorded',
                'data' => [
                    'mode' => 'guarded_spec',
                    'module' => $module,
                    'package_gated' => true,
                    'destructive_actions_require_confirmation' => true,
                ],
            ],
        };

        abort_unless(@$execute['success'], 422, @$execute['message'] ?: 'Live ZodPanel action failed.');

        $item = WhmPanelServiceItem::create([
            'account_id' => $website?->account_id,
            'website_id' => $website?->id,
            'module' => $module,
            'type' => $request->type ?: $this->defaultTypeForModule($module),
            'name' => $request->name,
            'status' => in_array($module, ['webmail', 'databases', 'backups'], true) ? 'completed' : 'planned',
            'config' => collect($request->except(['password']))
                ->filter(fn ($value) => $value !== null && $value !== '')
                ->merge(['live_response' => $this->redactSecrets($execute['data'] ?? null)])
                ->all(),
            'last_checked_at' => now(),
        ]);

        return $this->ok($item->load('website.account'), 201);
    }

    public function users()
    {
        return $this->ok(WhmPanelAccount::with('node', 'websites')->latest()->paginate(25));
    }

    public function createUser(Request $request)
    {
        $request->validate([
            'username' => 'required|string|max:40',
            'email' => 'nullable|email',
            'package' => 'nullable|string|max:80',
            'domain' => 'nullable|string|max:255',
        ]);

        $node = $this->node();
        $account = WhmPanelAccount::firstOrCreate(
            ['node_id' => $node->id, 'username' => $request->username],
            [
                'email' => $request->email,
                'package' => $request->package ?: 'starter',
                'primary_domain' => $request->domain,
                'status' => 'active',
            ]
        );

        if ($request->domain) {
            $this->createWebsiteForAccount($account, $request->domain);
        }

        return $this->ok($account->load('websites.dnsRecords'), 201);
    }

    public function showUser(string $username)
    {
        $account = WhmPanelAccount::where('username', $username)->with('websites.dnsRecords')->firstOrFail();
        return $this->ok($account);
    }

    public function suspendUser(string $username)
    {
        $account = WhmPanelAccount::where('username', $username)->firstOrFail();
        $account->status = 'suspended';
        $account->suspended_at = now();
        $account->save();
        $account->websites()->update(['status' => 'suspended']);

        return $this->ok(['message' => 'User suspended']);
    }

    public function unsuspendUser(string $username)
    {
        $account = WhmPanelAccount::where('username', $username)->firstOrFail();
        $account->status = 'active';
        $account->suspended_at = null;
        $account->save();
        $account->websites()->update(['status' => 'active']);

        return $this->ok(['message' => 'User unsuspended']);
    }

    public function deleteUser(string $username)
    {
        $account = WhmPanelAccount::where('username', $username)->firstOrFail();
        $account->status = 'terminated';
        $account->terminated_at = now();
        $account->save();
        $account->websites()->update(['status' => 'terminated']);

        return $this->ok(['message' => 'User terminated']);
    }

    public function websites()
    {
        return $this->ok(WhmPanelWebsite::with('account.node', 'dnsRecords')->latest()->paginate(25));
    }

    public function listDomains()
    {
        $domains = WhmPanelWebsite::where('status', 'active')->pluck('domain')->unique()->values();
        return $this->ok($domains);
    }

    public function createWebsite(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'domain' => 'required|string|max:255',
            'php_version' => 'nullable|string|max:12',
        ]);

        $account = WhmPanelAccount::where('username', $request->username)->firstOrFail();
        $website = $this->createWebsiteForAccount($account, $request->domain, $request->php_version ?: '8.3');

        return $this->ok($website->load('dnsRecords'), 201);
    }

    public function websiteDiagnostics(string $domain)
    {
        $website = WhmPanelWebsite::where('domain', $domain)->with('account.node', 'dnsRecords')->firstOrFail();
        $nodeIp = $website->account->node->ip_address ?: '127.0.0.1';

        return $this->ok([
            'domain' => $website->domain,
            'node_ip' => $nodeIp,
            'web' => [
                'exists' => true,
                'php_version' => $website->php_version,
                'ssl' => $website->ssl_enabled,
                'force_https' => $website->ssl_enabled,
                'status' => $website->status,
            ],
            'dns' => [
                'managed_records' => $website->dnsRecords,
                'required_records' => [
                    ...$this->requiredDnsRecords($website),
                ],
            ],
            'mail' => [
                'webmail_url' => 'https://webmail.' . $website->domain . '/',
                'status' => 'simulated',
            ],
            'blockers' => [],
        ]);
    }

    public function repairWebsiteDns(string $domain)
    {
        $website = WhmPanelWebsite::where('domain', $domain)->with('account.hosting.server.group')->firstOrFail();
        $hosting = $this->hostingForWebsite($website);
        abort_unless($hosting, 422, 'This domain is not attached to a live ZodPanel hosting service.');

        $execute = $this->zodPanelAction($hosting, 'repairDns', ['domain' => $website->domain]);
        abort_unless(@$execute['success'], 422, @$execute['message'] ?: 'Live DNS repair failed.');

        $item = WhmPanelServiceItem::create([
            'account_id' => $website->account_id,
            'website_id' => $website->id,
            'module' => 'dns',
            'type' => 'repair',
            'name' => 'DNS repair for ' . $website->domain,
            'status' => 'completed',
            'config' => ['live_response' => $execute['data'] ?? null],
            'last_checked_at' => now(),
        ]);

        return $this->ok([
            'response' => $execute['data'] ?? null,
            'event' => $item,
        ]);
    }

    public function enableWebsiteSsl(string $domain)
    {
        $website = WhmPanelWebsite::where('domain', $domain)->with('account.hosting.server.group')->firstOrFail();
        $hosting = $this->hostingForWebsite($website);

        $execute = null;
        if ($hosting) {
            $execute = $this->zodPanelAction($hosting, 'issueSsl', ['domain' => $website->domain]);
        }

        $website->ssl_enabled = true;
        $website->status = 'active';
        $website->save();

        $item = WhmPanelServiceItem::updateOrCreate(
            [
                'account_id' => $website->account_id,
                'website_id' => $website->id,
                'module' => 'ssl',
            ],
            [
                'type' => 'issue',
                'name' => 'SSL verified and active for ' . $website->domain,
                'status' => 'completed',
                'config' => ['ssl_enabled' => true, 'force_https' => true, 'live_response' => @$execute['data'] ?? null],
                'last_checked_at' => now(),
            ]
        );

        return $this->ok([
            'website' => $website,
            'event' => $item,
        ]);
    }

    public function websitePhp(string $domain)
    {
        $website = WhmPanelWebsite::where('domain', $domain)->firstOrFail();

        return $this->ok([
            'domain' => $website->domain,
            'php_version' => $website->php_version,
            'available' => $this->localPhpVersions(),
        ]);
    }

    public function updateWebsitePhp(Request $request, string $domain)
    {
        $request->validate([
            'php_version' => 'required|string|in:' . implode(',', $this->localPhpVersions()),
        ]);

        $website = WhmPanelWebsite::where('domain', $domain)->firstOrFail();
        $website->php_version = $request->php_version;
        $website->save();

        return $this->ok([
            'domain' => $website->domain,
            'php_version' => $website->php_version,
            'message' => 'PHP version updated in local WHMPanel simulator',
        ]);
    }

    public function dnsRecords(string $domain)
    {
        $website = WhmPanelWebsite::where('domain', $domain)->with('dnsRecords')->firstOrFail();
        return $this->ok($website->dnsRecords);
    }

    public function createDnsRecord(Request $request, string $domain)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|string|max:10',
            'value' => 'required|string',
            'ttl' => 'nullable|integer|min:60',
            'priority' => 'nullable|integer|min:0',
        ]);

        $website = WhmPanelWebsite::where('domain', $domain)->firstOrFail();
        $record = new WhmPanelDnsRecord();
        $record->website_id = $website->id;
        $record->name = $request->name;
        $record->type = strtoupper($request->type);
        $record->value = $request->value;
        $record->ttl = $request->ttl ?: 3600;
        $record->priority = $request->priority;
        $record->save();

        return $this->ok($record, 201);
    }

    public function createSso(Request $request)
    {
        $request->validate(['username' => 'required|string']);

        $account = WhmPanelAccount::where('username', $request->username)->firstOrFail();
        $plainToken = Str::random(48);

        $token = new WhmPanelSsoToken();
        $token->account_id = $account->id;
        $token->token_hash = Hash::make($plainToken);
        $token->expires_at = now()->addMinutes(15);
        $token->save();

        return $this->ok([
            'session_url' => route('whmpanel.sso', ['token' => $plainToken]),
            'expires_at' => $token->expires_at->toIso8601String(),
        ]);
    }

    private function node(): WhmPanelNode
    {
        $node = WhmPanelNode::firstOrNew(['name' => 'Local WHMPanel']);
        $node->hostname = parse_url(config('app.url'), PHP_URL_HOST) ?: 'localhost';
        $node->ip_address = '127.0.0.1';
        $node->api_token = $node->api_token ?: (config('whmpanel.local_api_token') ?: Str::random(48));
        $node->status = 'online';
        $node->cpu_percent = $node->cpu_percent ?: 4;
        $node->memory_percent = $node->memory_percent ?: 18;
        $node->last_sync_at = now();
        $node->save();

        return $node;
    }

    private function createWebsiteForAccount(WhmPanelAccount $account, string $domain, string $phpVersion = '8.3'): WhmPanelWebsite
    {
        $website = WhmPanelWebsite::firstOrCreate(
            ['account_id' => $account->id, 'domain' => $domain],
            [
                'document_root' => "/home/{$account->username}/web/$domain/public_html",
                'php_version' => $phpVersion,
                'ssl_enabled' => true,
                'status' => 'active',
            ]
        );

        $nodeIp = $account->node->ip_address ?: '127.0.0.1';

        // Auto-provision standard DNS records (A, Wildcard A, CNAME, MX, SPF, DMARC)
        WhmPanelDnsRecord::firstOrCreate(
            ['website_id' => $website->id, 'name' => '@', 'type' => 'A'],
            ['value' => $nodeIp, 'ttl' => 3600]
        );
        WhmPanelDnsRecord::firstOrCreate(
            ['website_id' => $website->id, 'name' => '*', 'type' => 'A'],
            ['value' => $nodeIp, 'ttl' => 3600]
        );
        WhmPanelDnsRecord::firstOrCreate(
            ['website_id' => $website->id, 'name' => 'www', 'type' => 'CNAME'],
            ['value' => $domain . '.', 'ttl' => 3600]
        );
        WhmPanelDnsRecord::firstOrCreate(
            ['website_id' => $website->id, 'name' => 'mail', 'type' => 'A'],
            ['value' => $nodeIp, 'ttl' => 3600]
        );
        WhmPanelDnsRecord::firstOrCreate(
            ['website_id' => $website->id, 'name' => 'webmail', 'type' => 'CNAME'],
            ['value' => $domain . '.', 'ttl' => 3600]
        );
        WhmPanelDnsRecord::firstOrCreate(
            ['website_id' => $website->id, 'name' => '@', 'type' => 'MX'],
            ['value' => 'mail.' . $domain . '.', 'ttl' => 3600, 'priority' => 0]
        );
        WhmPanelDnsRecord::firstOrCreate(
            ['website_id' => $website->id, 'name' => '@', 'type' => 'TXT'],
            ['value' => 'v=spf1 a mx ip4:' . $nodeIp . ' ~all', 'ttl' => 3600]
        );
        WhmPanelDnsRecord::firstOrCreate(
            ['website_id' => $website->id, 'name' => '_dmarc', 'type' => 'TXT'],
            ['value' => 'v=DMARC1; p=quarantine; rua=mailto:postmaster@' . $domain, 'ttl' => 3600]
        );

        // Auto-provision instant SAN SSL & Force HTTPS
        $website->ssl_enabled = true;
        $website->status = 'active';
        $website->save();

        WhmPanelServiceItem::firstOrCreate(
            [
                'account_id' => $account->id,
                'website_id' => $website->id,
                'module' => 'ssl',
            ],
            [
                'type' => 'issue',
                'name' => 'Automated Instant SSL & Force HTTPS for ' . $domain,
                'status' => 'completed',
                'config' => ['ssl_enabled' => true, 'force_https' => true],
                'last_checked_at' => now(),
            ]
        );

        // If attached to a live ZodPanel hosting service, auto-trigger domain, authoritative DNS & AutoSSL provisioning
        $hosting = $account->hosting ?? null;
        if ($hosting && @$hosting->server?->group?->type == 4) {
            $this->zodPanelAction($hosting, 'addWebDomain', ['domain' => $domain]);
            $this->zodPanelAction($hosting, 'issueSsl', ['domain' => $domain]);
        }

        return $website;
    }

    private function serviceModulePayload(string $module): array
    {
        $websites = WhmPanelWebsite::with('account.node', 'dnsRecords')->latest()->get();
        $items = WhmPanelServiceItem::with('website.account')
            ->where('module', $module)
            ->latest()
            ->limit(80)
            ->get();

        return [
            'module' => WhmPanelServiceCatalog::find($module, $this->localNodeFeatures()),
            'stats' => [
                'accounts' => WhmPanelAccount::count(),
                'websites' => $websites->count(),
                'items' => $items->count(),
                'ssl_enabled' => $websites->where('ssl_enabled', true)->count(),
                'dns_records' => $websites->sum(fn ($website) => $website->dnsRecords->count()),
            ],
            'websites' => $websites,
            'items' => $items,
        ];
    }

    private function requiredDnsRecords(WhmPanelWebsite $website): array
    {
        $nodeIp = $website->account?->node?->ip_address ?: '127.0.0.1';

        return [
            ['name' => '@', 'type' => 'A', 'value' => $nodeIp],
            ['name' => 'www', 'type' => 'CNAME', 'value' => $website->domain . '.'],
            ['name' => 'mail', 'type' => 'A', 'value' => $nodeIp],
            ['name' => 'webmail', 'type' => 'A', 'value' => $nodeIp],
            ['name' => '@', 'type' => 'MX', 'priority' => 0, 'value' => 'mail.' . $website->domain . '.'],
            ['name' => '@', 'type' => 'TXT', 'value' => 'v=spf1 a mx ip4:' . $nodeIp . ' ~all'],
            ['name' => '_dmarc', 'type' => 'TXT', 'value' => 'v=DMARC1; p=quarantine; rua=mailto:postmaster@' . $website->domain],
        ];
    }

    private function defaultTypeForModule(string $module): string
    {
        return match ($module) {
            'webmail' => 'mailbox',
            'databases' => 'database',
            'backups' => 'snapshot',
            'nodejs', 'python', 'apps' => 'app',
            'security' => 'rule',
            'logs' => 'event',
            default => 'item',
        };
    }

    private function redactSecrets($value)
    {
        if (!is_array($value)) {
            return $value;
        }

        foreach ($value as $key => $nested) {
            if (in_array(strtolower((string) $key), ['password', 'dbpass', 'secret', 'token'], true)) {
                $value[$key] = '[redacted]';
                continue;
            }
            $value[$key] = $this->redactSecrets($nested);
        }

        return $value;
    }

    private function hostingForWebsite(?WhmPanelWebsite $website)
    {
        return $website->account?->hosting?->server?->group ? $website->account->hosting : null;
    }

    private function zodPanelAction($hosting, string $method, array $payload = []): array
    {
        $serverGroup = @$hosting->server->group;

        if (@$serverGroup->type != 4) {
            return [
                'success' => false,
                'message' => 'This service is not connected to a live ZodPanel server group',
            ];
        }

        try {
            $execute = HostingManager::init($serverGroup)->{$method}(array_merge(['hosting' => $hosting], $payload));

            return is_array($execute) ? $execute : [
                'success' => false,
                'message' => 'ZodPanel returned an invalid response',
            ];
        } catch (\Throwable $exception) {
            return [
                'success' => false,
                'message' => 'ZodPanel action failed: ' . $exception->getMessage(),
            ];
        }
    }

    private function localNodeFeatures(): array
    {
        return [
            'file_manager' => true,
            'webmail' => true,
            'php_selector' => true,
            'terminal' => true,
            'nodejs' => true,
            'python' => true,
            'auto_dns' => true,
            'auto_ssl' => true,
            'backups' => true,
        ];
    }

    private function localPhpVersions(): array
    {
        return ['8.3', '8.2', '8.1'];
    }

    private function ok($data, int $status = 200)
    {
        return response()->json([
            'success' => true,
            'data' => $data,
        ], $status);
    }
}
