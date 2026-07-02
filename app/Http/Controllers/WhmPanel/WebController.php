<?php

namespace App\Http\Controllers\WhmPanel;

use App\HostingModule\HostingManager;
use App\Http\Controllers\Controller;
use App\Models\WhmPanelAccount;
use App\Models\WhmPanelDnsRecord;
use App\Models\WhmPanelWebsite;
use App\Models\WhmPanelNode;
use App\Models\WhmPanelServiceItem;
use App\Models\WhmPanelSsoToken;
use App\Support\WhmPanelServiceCatalog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class WebController extends Controller
{
    public function dashboard()
    {
        $nodes = WhmPanelNode::withCount('accounts')->latest()->get();
        $accounts = WhmPanelAccount::with('node', 'websites')->latest()->limit(10)->get();
        $primaryNode = $nodes->first();
        $serviceModules = WhmPanelServiceCatalog::modules([
            'file_manager' => true,
            'webmail' => true,
            'php_selector' => true,
            'terminal' => false,
            'nodejs' => false,
            'python' => false,
            'auto_dns' => true,
            'auto_ssl' => true,
            'backups' => true,
        ]);
        $serviceSummary = WhmPanelServiceCatalog::summary($serviceModules);
        $pageTitle = 'WHMPanel';

        return view('whmpanel.dashboard', compact('pageTitle', 'nodes', 'accounts', 'primaryNode', 'serviceModules', 'serviceSummary'));
    }

    public function services()
    {
        $serviceModules = WhmPanelServiceCatalog::modules($this->localNodeFeatures());
        $serviceSummary = WhmPanelServiceCatalog::summary($serviceModules);
        $pageTitle = 'WHMPanel Services';

        return view('whmpanel.services', compact('pageTitle', 'serviceModules', 'serviceSummary'));
    }

    public function serviceModule(string $module)
    {
        $serviceModule = WhmPanelServiceCatalog::find($module, $this->localNodeFeatures());

        if (!$serviceModule) {
            abort(404);
        }

        $payload = $this->serviceModulePayload($module);
        $websites = WhmPanelWebsite::with('account')->orderBy('domain')->get();
        $pageTitle = $serviceModule['name'];

        return view('whmpanel.service_module', compact('pageTitle', 'serviceModule', 'payload', 'websites'));
    }

    public function storeServiceItem(Request $request, string $module)
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

        if (in_array($module, ['webmail', 'databases', 'backups'], true) && !$hosting) {
            return back()->with('status', 'This domain is not attached to a live ZodPanel hosting service.');
        }

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
                'message' => $this->moduleLabel($module) . ' guarded implementation spec recorded. Live node execution requires the module-specific bridge worker.',
                'data' => [
                    'mode' => 'guarded_spec',
                    'module' => $module,
                    'package_gated' => true,
                    'destructive_actions_require_confirmation' => true,
                ],
            ],
        };

        if (!@$execute['success']) {
            return back()->with('status', @$execute['message'] ?: 'Live ZodPanel action failed.');
        }

        $item = new WhmPanelServiceItem();
        $item->account_id = $website?->account_id;
        $item->website_id = $website?->id;
        $item->module = $module;
        $item->type = $request->type ?: $this->defaultTypeForModule($module);
        $item->name = $request->name;
        $item->status = in_array($module, ['webmail', 'databases', 'backups'], true) ? 'completed' : 'planned';
        $item->config = collect($request->except(['_token', 'password']))
            ->filter(fn ($value) => $value !== null && $value !== '')
            ->merge(['live_response' => $this->redactSecrets($execute['data'] ?? null)])
            ->all();
        $item->last_checked_at = now();
        $item->save();

        return back()->with('status', in_array($module, ['webmail', 'databases', 'backups'], true)
            ? $this->moduleLabel($module) . ' live action completed: ' . (@$execute['message'] ?: $item->name)
            : @$execute['message']);
    }

    public function websites()
    {
        $websites = WhmPanelWebsite::with('account.node', 'dnsRecords')->latest()->paginate(20);
        $pageTitle = 'WHMPanel Websites';

        return view('whmpanel.websites', compact('pageTitle', 'websites'));
    }

    public function website(string $domain)
    {
        $website = WhmPanelWebsite::where('domain', $domain)->with('account.node', 'dnsRecords')->firstOrFail();
        $diagnostics = $this->diagnosticsForWebsite($website);
        $phpVersions = $this->localPhpVersions();
        $pageTitle = 'Domain Diagnostics';

        return view('whmpanel.website', compact('pageTitle', 'website', 'diagnostics', 'phpVersions'));
    }

    public function updateWebsitePhp(Request $request, string $domain)
    {
        $request->validate([
            'php_version' => 'required|string|in:' . implode(',', $this->localPhpVersions()),
        ]);

        $website = WhmPanelWebsite::where('domain', $domain)->firstOrFail();
        $website->php_version = $request->php_version;
        $website->save();

        return back()->with('status', 'PHP version updated for ' . $website->domain);
    }

    public function repairWebsiteDns(string $domain)
    {
        $website = WhmPanelWebsite::where('domain', $domain)->with('account.hosting.server.group')->firstOrFail();
        $hosting = $this->hostingForWebsite($website);

        if (!$hosting) {
            return back()->with('status', 'This domain is not attached to a live ZodPanel hosting service.');
        }

        $execute = $this->zodPanelAction($hosting, 'repairDns', ['domain' => $website->domain]);

        if (!@$execute['success']) {
            return back()->with('status', @$execute['message'] ?: 'Live DNS repair failed.');
        }

        WhmPanelServiceItem::create([
            'account_id' => $website->account_id,
            'website_id' => $website->id,
            'module' => 'dns',
            'type' => 'repair',
            'name' => 'DNS repair for ' . $website->domain,
            'status' => 'completed',
            'config' => ['live_response' => $execute['data'] ?? null],
            'last_checked_at' => now(),
        ]);

        return back()->with('status', 'Live DNS records repaired for ' . $website->domain);
    }

    public function enableWebsiteSsl(string $domain)
    {
        $website = WhmPanelWebsite::where('domain', $domain)->with('account.hosting.server.group')->firstOrFail();
        $hosting = $this->hostingForWebsite($website);

        if (!$hosting) {
            return back()->with('status', 'This domain is not attached to a live ZodPanel hosting service.');
        }

        $execute = $this->zodPanelAction($hosting, 'issueSsl', ['domain' => $website->domain]);

        if (!@$execute['success']) {
            return back()->with('status', @$execute['message'] ?: 'Live SSL repair failed.');
        }

        $website->ssl_enabled = (bool) data_get($execute, 'data.0.ssl.installed', $website->ssl_enabled);
        $website->save();

        WhmPanelServiceItem::create([
            'account_id' => $website->account_id,
            'website_id' => $website->id,
            'module' => 'ssl',
            'type' => 'issue',
            'name' => 'SSL enabled for ' . $website->domain,
            'status' => 'completed',
            'config' => ['live_response' => $execute['data'] ?? null],
            'last_checked_at' => now(),
        ]);

        return back()->with('status', 'Live SSL repair attempted for ' . $website->domain);
    }

    public function openTerminal(Request $request)
    {
        $request->validate([
            'website_id' => 'nullable|exists:whm_panel_websites,id',
            'path' => 'nullable|string|max:255',
        ]);

        $website = $request->website_id ? WhmPanelWebsite::with('account')->findOrFail($request->website_id) : null;

        WhmPanelServiceItem::create([
            'account_id' => $website?->account_id,
            'website_id' => $website?->id,
            'module' => 'terminal',
            'type' => 'open',
            'name' => 'Terminal session' . ($website ? ' for ' . $website->domain : ''),
            'status' => 'recorded',
            'config' => [
                'path' => $request->path ?: ($website?->document_root ?: '/home'),
                'policy' => 'non-root browser terminal audit',
            ],
            'last_checked_at' => now(),
        ]);

        return back()->with('status', 'Terminal audit recorded. Live terminal opens on eligible non-root packages only.');
    }

    public function sso(Request $request)
    {
        $request->validate(['token' => 'required|string']);

        $tokens = WhmPanelSsoToken::whereNull('used_at')
            ->where('expires_at', '>', now())
            ->with('account.node')
            ->latest()
            ->get();

        foreach ($tokens as $token) {
            if (Hash::check($request->token, $token->token_hash)) {
                $token->used_at = now();
                $token->save();

                session(['whmpanel_account_id' => $token->account_id]);
                return to_route('whmpanel.dashboard');
            }
        }

        abort(403, 'Invalid or expired WHMPanel SSO token');
    }

    private function diagnosticsForWebsite(WhmPanelWebsite $website): array
    {
        $nodeIp = $website->account->node->ip_address ?: '127.0.0.1';

        return [
            'domain' => $website->domain,
            'node_ip' => $nodeIp,
            'web' => [
                'exists' => true,
                'php_version' => $website->php_version,
                'ssl' => $website->ssl_enabled,
                'force_https' => $website->ssl_enabled,
                'status' => $website->status,
            ],
            'mail' => [
                'webmail_url' => 'https://webmail.' . $website->domain . '/',
                'status' => 'simulated',
            ],
            'dns' => [
                'managed_records' => $website->dnsRecords,
                'required_records' => [
                    ...$this->requiredDnsRecords($website),
                ],
            ],
            'blockers' => [],
        ];
    }

    private function serviceModulePayload(string $module): array
    {
        $websites = WhmPanelWebsite::with('account.node', 'dnsRecords')->latest()->get();
        $items = WhmPanelServiceItem::with('website.account')
            ->where('module', $module)
            ->latest()
            ->limit(80)
            ->get();

        $sslEnabled = $websites->where('ssl_enabled', true)->count();
        $dnsRecordCount = $websites->sum(fn ($website) => $website->dnsRecords->count());

        return [
            'stats' => match ($module) {
                'webmail' => [
                    ['label' => 'Mail domains', 'value' => $websites->count(), 'tone' => 'online'],
                    ['label' => 'Mailboxes', 'value' => $items->where('type', 'mailbox')->count(), 'tone' => 'planned'],
                    ['label' => 'Repair events', 'value' => $items->where('type', 'repair')->count(), 'tone' => 'online'],
                ],
                'dns' => [
                    ['label' => 'Zones', 'value' => $websites->count(), 'tone' => 'online'],
                    ['label' => 'Managed records', 'value' => $dnsRecordCount, 'tone' => 'planned'],
                    ['label' => 'Repairs', 'value' => $items->where('type', 'repair')->count(), 'tone' => 'online'],
                ],
                'ssl' => [
                    ['label' => 'SSL enabled', 'value' => $sslEnabled, 'tone' => 'online'],
                    ['label' => 'Needs SSL', 'value' => $websites->count() - $sslEnabled, 'tone' => 'blocked'],
                    ['label' => 'SSL events', 'value' => $items->count(), 'tone' => 'planned'],
                ],
                'databases' => [
                    ['label' => 'Databases', 'value' => $items->where('type', 'database')->count(), 'tone' => 'online'],
                    ['label' => 'Users', 'value' => $items->where('type', 'user')->count(), 'tone' => 'planned'],
                    ['label' => 'phpMyAdmin', 'value' => 'Ready', 'tone' => 'online'],
                ],
                'backups' => [
                    ['label' => 'Backups', 'value' => $items->count(), 'tone' => 'online'],
                    ['label' => 'Restores', 'value' => $items->where('type', 'restore')->count(), 'tone' => 'planned'],
                    ['label' => 'Retention', 'value' => 'Package', 'tone' => 'planned'],
                ],
                'nodejs', 'python', 'apps' => [
                    ['label' => 'Apps', 'value' => $items->count(), 'tone' => 'online'],
                    ['label' => 'Running', 'value' => $items->where('status', 'running')->count(), 'tone' => 'online'],
                    ['label' => 'Logs', 'value' => $items->where('type', 'log')->count(), 'tone' => 'planned'],
                ],
                'logs' => [
                    ['label' => 'Events', 'value' => $items->count(), 'tone' => 'online'],
                    ['label' => 'Domains', 'value' => $websites->count(), 'tone' => 'planned'],
                    ['label' => 'Search', 'value' => 'Ready', 'tone' => 'online'],
                ],
                'security' => [
                    ['label' => 'Rules', 'value' => $items->count(), 'tone' => 'online'],
                    ['label' => 'SSL domains', 'value' => $sslEnabled, 'tone' => 'online'],
                    ['label' => '2FA policy', 'value' => 'Planned', 'tone' => 'planned'],
                ],
                'terminal' => [
                    ['label' => 'Audit events', 'value' => $items->count(), 'tone' => 'online'],
                    ['label' => 'Policy', 'value' => 'Non-root', 'tone' => 'planned'],
                    ['label' => 'Eligible plans', 'value' => 'Developer', 'tone' => 'planned'],
                ],
                default => [
                    ['label' => 'Domains', 'value' => $websites->count(), 'tone' => 'online'],
                    ['label' => 'Items', 'value' => $items->count(), 'tone' => 'planned'],
                    ['label' => 'Status', 'value' => 'Ready', 'tone' => 'online'],
                ],
            },
            'websites' => $websites,
            'items' => $items,
            'live' => $this->liveRowsForModule($module, $websites),
        ];
    }

    private function liveRowsForModule(string $module, $websites): array
    {
        if (!in_array($module, ['webmail', 'databases', 'backups'], true)) {
            return [];
        }

        $rows = [];
        $seenHostings = [];

        foreach ($websites as $website) {
            $website->loadMissing('account.hosting.server.group');
            $hosting = $this->hostingForWebsite($website);

            if (!$hosting) {
                continue;
            }

            if (in_array($module, ['databases', 'backups'], true)) {
                if (in_array($hosting->id, $seenHostings, true)) {
                    continue;
                }
                $seenHostings[] = $hosting->id;
            }

            $execute = match ($module) {
                'webmail' => $this->zodPanelAction($hosting, 'mailAccounts', ['domain' => $website->domain]),
                'databases' => $this->zodPanelAction($hosting, 'databases'),
                'backups' => $this->zodPanelAction($hosting, 'backups'),
            };

            if (!@$execute['success']) {
                $rows[] = [
                    'name' => $website->domain,
                    'domain' => $website->domain,
                    'type' => 'error',
                    'status' => @$execute['message'] ?: 'Live query failed',
                    'meta' => '',
                ];
                continue;
            }

            foreach (($execute['data'] ?? []) as $key => $value) {
                $rows[] = [
                    'name' => (string) $key,
                    'domain' => $website->domain,
                    'type' => $module,
                    'status' => is_array($value) ? ($value['SUSPENDED'] ?? $value['TYPE'] ?? 'active') : 'active',
                    'meta' => is_array($value) ? $this->compactMeta($value) : (string) $value,
                ];
            }
        }

        return $rows;
    }

    private function compactMeta(array $value): string
    {
        $parts = [];

        foreach (['U_DISK', 'SIZE', 'QUOTA', 'DBUSER', 'HOST', 'TYPE', 'DATE'] as $key) {
            if (isset($value[$key]) && $value[$key] !== '') {
                $parts[] = $key . ': ' . $value[$key];
            }
        }

        return implode(' · ', array_slice($parts, 0, 4));
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

    private function moduleLabel(string $module): string
    {
        return WhmPanelServiceCatalog::find($module, $this->localNodeFeatures())['name'] ?? 'Service';
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
}
