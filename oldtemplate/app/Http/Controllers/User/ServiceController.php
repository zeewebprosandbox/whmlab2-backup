<?php

namespace App\Http\Controllers\User;

use App\HostingModule\HostingManager;
use App\Http\Controllers\Controller;
use App\Models\Hosting;
use App\Models\CancelRequest;
use App\Models\Server;
use Illuminate\Http\Request;

class ServiceController extends Controller{
    
    public function list(Request $request){
        $pageTitle = 'Service List';
        $user = auth()->user();
        $selectedServiceGroup = $request->query('service');
        $serviceGroups = $this->serviceGroupCounts($user);

        $servicesQuery = Hosting::whereBelongsTo($user)
            ->with('product.serviceCategory', 'server.group')
            ->orderBy('id', 'DESC');

        $this->applyServiceGroupFilter($servicesQuery, $selectedServiceGroup);

        $services = $servicesQuery->paginate(getPaginate())->withQueryString();

        return view('Template::user.service.list', compact('pageTitle', 'services', 'serviceGroups', 'selectedServiceGroup'));
    }

    public function details($id){

        $pageTitle = 'Service Details';
        $service = Hosting::whereBelongsTo(auth()->user())
            ->with('hostingConfigs.select', 'hostingConfigs.option', 'product.getConfigs.group.options', 'server.group')
            ->findOrFail($id);

        $server = @$service->server;
        $serverGroup = @$server->group;
        $product = $service->product;
        $status = $service->status;
        $cancelRequestTypes = CancelRequest::type();

        $accountSummary = null;
        $hasAccount = false;
        $zodPanelDiagnostics = null;
        $diskUsagePercent = 0;
        $bwUsagePercent = 0;

        $databases = [];
        $mailAccounts = [];
        $dnsRecords = [];
        $sslInfo = null;
        $phpVersion = '8.3';
        $availablePhp = [];

        if ($serverGroup && $status == 1) {
            $execute = HostingManager::init($serverGroup)->accountSummary($service);
            $accountSummary = @$execute['processed_data'];
            $hasAccount = (bool) @$execute['raw_data'];
            $diskUsagePercent = (float) (@$accountSummary['disk_usage_percent'] ?: 0);
            $bwUsagePercent = (float) (@$accountSummary['bw_usage_percent'] ?: 0);

            if (@$serverGroup->type == 4 && $hasAccount) {
                $diagRes = $this->zodPanelAction($service, 'serviceDiagnostics');
                $zodPanelDiagnostics = @$diagRes['data'] ?: [];

                $dbRes = $this->zodPanelAction($service, 'databases');
                $databases = @$dbRes['data'] ?: [];

                $mailRes = $this->zodPanelAction($service, 'mailAccounts', ['domain' => $service->domain]);
                $mailAccounts = @$mailRes['data'] ?: [];

                $delivRes = $this->zodPanelAction($service, 'mailDeliverabilityDiagnostics', ['domain' => $service->domain]);
                $mailDeliverability = @$delivRes['data'] ?: [];

                $phpRes = $this->zodPanelAction($service, 'phpOptions');
                $phpData = @$phpRes['data'] ?: [];
                $phpVersion = $phpData['current'] ?? $phpData['backend'] ?? 'default';
                $availablePhp = $phpData['available'] ?? $phpData['backends'] ?? [];

                $dnsRecords = $zodPanelDiagnostics['dns']['required_records'] ?? $zodPanelDiagnostics['dns_records'] ?? [];
                $sslInfo = $zodPanelDiagnostics['web'] ?? $zodPanelDiagnostics['ssl'] ?? null;
            }
        }

        $nodeHost = $server?->ip_address ?: (parse_url($server?->hostname ?: '', PHP_URL_HOST) ?: '169.58.176.53');
        $ssoLinks = [
            'panel' => route('user.login.hosting', $service->id),
            'phpmyadmin' => "https://{$nodeHost}:8083/open/phpmyadmin/",
            'file_manager' => "https://{$nodeHost}:8083/fm/?domain=" . urlencode($service->domain ?: ''),
            'webmail' => "https://webmail." . ($service->domain ?: 'local') . "/",
        ];

        $nameservers = collect([
            ['label' => 'NS1', 'host' => $service->ns1 ?: @$server->ns1, 'ip' => @$server->ns1_ip],
            ['label' => 'NS2', 'host' => $service->ns2 ?: @$server->ns2, 'ip' => @$server->ns2_ip],
            ['label' => 'NS3', 'host' => @$server->ns3, 'ip' => @$server->ns3_ip],
            ['label' => 'NS4', 'host' => @$server->ns4, 'ip' => @$server->ns4_ip],
        ])->filter(fn ($record) => !empty($record['host']))->values();

        return view('Template::user.service.details', compact(
            'pageTitle',
            'service',
            'accountSummary',
            'serverGroup',
            'cancelRequestTypes',
            'diskUsagePercent',
            'bwUsagePercent',
            'product',
            'status',
            'hasAccount',
            'nameservers',
            'zodPanelDiagnostics',
            'databases',
            'mailAccounts',
            'mailDeliverability',
            'dnsRecords',
            'sslInfo',
            'phpVersion',
            'availablePhp',
            'ssoLinks'
        ));
    }

    public function cancelRequest(Request $request){

        $request->validate([ 
            'id' => 'required|integer',
            'reason' => 'required',
            'cancellation_type' => 'required|in:'.CancelRequest::type(true),
        ]);

        $service = Hosting::whereBelongsTo(auth()->user())->whereDoesntHave('cancelRequest')->findOrFail($request->id);

        $cancelRequest = new CancelRequest();
        $cancelRequest->user_id = auth()->user()->id;
        $cancelRequest->hosting_id = $service->id;
        $cancelRequest->reason = $request->reason;
        $cancelRequest->type = $request->cancellation_type; 
        /**
        * For knowing about the type 
        * @see \App\Models\CancelRequest go to type method 
        */
        $cancelRequest->save();

        $notify[] = ['success', 'Your cancellation request submitted successfully'];
        return back()->withNotify($notify);
    }

    public function repairZodPanelWebmail(Request $request, $id)
    {
        $service = Hosting::whereBelongsTo(auth()->user())
            ->with('server.group')
            ->findOrFail($id);

        if ($service->status != 1) {
            $notify[] = ['error', 'This action requires an active hosting service.'];
            return back()->withNotify($notify);
        }

        $execute = $this->zodPanelAction($service, 'repairWebmail', [
            'domain' => $service->domain,
            'create_mail_domain' => true,
        ]);

        $notify[] = [@$execute['success'] ? 'success' : 'error', @$execute['message'] ?: 'ZodPanel webmail repair completed'];
        return back()->withNotify($notify);
    }

    public function repairMailDeliverability(Request $request, $id)
    {
        $service = Hosting::whereBelongsTo(auth()->user())
            ->with('server.group')
            ->findOrFail($id);

        if ($service->status != 1) {
            $notify[] = ['error', 'This action requires an active hosting service.'];
            return back()->withNotify($notify);
        }

        $execute = $this->zodPanelAction($service, 'repairMailDeliverability', [
            'domain' => $service->domain,
            'repair_dns' => true,
            'force_dkim' => true,
        ]);

        $notify[] = [@$execute['success'] ? 'success' : 'error', @$execute['message'] ?: 'Mail deliverability, 2048-bit DKIM, SPF, and DMARC synchronized for 100% inbox delivery'];
        return back()->withNotify($notify);
    }

    public function issueSsl($id)
    {
        $service = Hosting::whereBelongsTo(auth()->user())
            ->with('server.group')
            ->findOrFail($id);

        if ($service->status != 1) {
            $notify[] = ['error', 'This action requires an active hosting service.'];
            return back()->withNotify($notify);
        }

        $execute = $this->zodPanelAction($service, 'issueSsl', [
            'domain' => $service->domain,
        ]);

        $notify[] = [@$execute['success'] ? 'success' : 'error', @$execute['message'] ?: 'Auto-SSL & Force HTTPS issued successfully'];
        return back()->withNotify($notify);
    }

    public function repairDns($id)
    {
        $service = Hosting::whereBelongsTo(auth()->user())
            ->with('server.group')
            ->findOrFail($id);

        if ($service->status != 1) {
            $notify[] = ['error', 'This action requires an active hosting service.'];
            return back()->withNotify($notify);
        }

        $execute = $this->zodPanelAction($service, 'repairDns', [
            'domain' => $service->domain,
        ]);

        $notify[] = [@$execute['success'] ? 'success' : 'error', @$execute['message'] ?: 'Live DNS records repaired successfully'];
        return back()->withNotify($notify);
    }

    public function createMailbox(Request $request, $id)
    {
        $request->validate([
            'v_account' => 'required|string|max:64',
            'v_domain' => 'nullable|string|max:255',
            'v_password' => 'required|string|min:6',
            'v_quota' => 'nullable|string',
        ]);

        $service = Hosting::whereBelongsTo(auth()->user())
            ->with('server.group')
            ->findOrFail($id);

        if ($service->status != 1) {
            $notify[] = ['error', 'This action requires an active hosting service.'];
            return back()->withNotify($notify);
        }

        $execute = $this->zodPanelAction($service, 'createMailAccount', [
            'account' => $request->v_account,
            'domain' => $request->v_domain ?: $service->domain,
            'password' => $request->v_password,
            'quota_mb' => (int) ($request->v_quota ?: 1000),
        ]);

        $notify[] = [@$execute['success'] ? 'success' : 'error', @$execute['message'] ?: 'Mailbox ' . $request->v_account . '@' . ($request->v_domain ?: $service->domain) . ' created successfully'];
        return back()->withNotify($notify);
    }

    public function createDatabase(Request $request, $id)
    {
        $request->validate([
            'database' => 'required|string|max:64',
            'dbuser' => 'nullable|string|max:64',
            'password' => 'required|string|min:6',
        ]);

        $service = Hosting::whereBelongsTo(auth()->user())
            ->with('server.group')
            ->findOrFail($id);

        if ($service->status != 1) {
            $notify[] = ['error', 'This action requires an active hosting service.'];
            return back()->withNotify($notify);
        }

        $execute = $this->zodPanelAction($service, 'createDatabase', [
            'database' => $request->database,
            'db_user' => $request->dbuser ?: $request->database,
            'password' => $request->password,
        ]);

        $notify[] = [@$execute['success'] ? 'success' : 'error', @$execute['message'] ?: 'Database ' . $request->database . ' created successfully'];
        return back()->withNotify($notify);
    }

    public function changePhp(Request $request, $id)
    {
        $request->validate([
            'php_version' => 'required|string',
        ]);

        $service = Hosting::whereBelongsTo(auth()->user())
            ->with('server.group')
            ->findOrFail($id);

        if ($service->status != 1) {
            $notify[] = ['error', 'This action requires an active hosting service.'];
            return back()->withNotify($notify);
        }

        $execute = $this->zodPanelAction($service, 'changeDomainPhp', [
            'template' => $request->php_version,
            'php_version' => $request->php_version,
        ]);

        $notify[] = [@$execute['success'] ? 'success' : 'error', @$execute['message'] ?: 'PHP version updated to ' . $request->php_version];
        return back()->withNotify($notify);
    }

    private function serviceGroupCounts($user)
    {
        $roles = collect(Server::serviceRoles())->except(['any', 'domain']);
        $counts = array_fill_keys($roles->keys()->all(), 0);

        Hosting::whereBelongsTo($user)
            ->with('product.serviceCategory', 'server')
            ->get()
            ->each(function ($service) use (&$counts) {
                $key = @$service->server->service_role ?: Server::roleForProduct($service->product);

                if (array_key_exists($key, $counts)) {
                    $counts[$key]++;
                }
            });

        return $roles
            ->map(fn ($label, $key) => [
                'key' => $key,
                'label' => $label,
                'count' => $counts[$key] ?? 0,
            ])
            ->filter(fn ($group) => $group['count'] > 0)
            ->values();
    }

    private function applyServiceGroupFilter($query, $selectedServiceGroup): void
    {
        if (!$selectedServiceGroup || $selectedServiceGroup === 'all') {
            return;
        }

        $needles = match ($selectedServiceGroup) {
            'premium_shared' => ['premium', 'business', 'pro shared'],
            'vps' => ['vps', 'kvm', 'nvme'],
            'dedicated' => ['dedicated'],
            'mail' => ['mail', 'email'],
            'rdp' => ['rdp', 'remote desktop'],
            'radio' => ['radio', 'shoutcast'],
            default => ['shared', 'hosting'],
        };

        $query->where(function ($hosting) use ($selectedServiceGroup, $needles) {
            $hosting->whereHas('server', function ($server) use ($selectedServiceGroup) {
                $server->where('service_role', $selectedServiceGroup);
            })->orWhereHas('product', function ($product) use ($needles) {
                $product->where(function ($productSearch) use ($needles) {
                    foreach ($needles as $needle) {
                        $productSearch->orWhere('name', 'like', "%{$needle}%");
                    }
                })->orWhereHas('serviceCategory', function ($category) use ($needles) {
                    $category->where(function ($categorySearch) use ($needles) {
                        foreach ($needles as $needle) {
                            $categorySearch->orWhere('name', 'like', "%{$needle}%");
                        }
                    });
                });
            });
        });
    }

    private function zodPanelAction($service, string $method, array $payload = []): array
    {
        $serverGroup = @$service->server->group;

        if (@$serverGroup->type != 4) {
            return [
                'success' => false,
                'message' => 'This service is not connected to a ZodPanel server group',
            ];
        }

        try {
            $data = $payload ? array_merge(['hosting' => $service], $payload) : $service;
            $execute = HostingManager::init($serverGroup)->{$method}($data);

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

}
