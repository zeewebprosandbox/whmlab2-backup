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
        
        $execute = HostingManager::init($serverGroup)->accountSummary($service);
        $accountSummary = @$execute['processed_data'];
        $cancelRequestTypes = CancelRequest::type();

        $product = $service->product;
        $status = $service->status;  
        $diskUsagePercent = @$accountSummary['disk_usage_percent'];
        $hasAccount = @$execute['raw_data'];
        $zodPanelDiagnostics = null;

        if (@$serverGroup->type == 4 && $hasAccount) {
            $zodPanelDiagnostics = $this->zodPanelAction($service, 'serviceDiagnostics');
        }

        $nameservers = collect([
            ['label' => 'NS1', 'host' => $service->ns1 ?: @$server->ns1, 'ip' => @$server->ns1_ip],
            ['label' => 'NS2', 'host' => $service->ns2 ?: @$server->ns2, 'ip' => @$server->ns2_ip],
            ['label' => 'NS3', 'host' => @$server->ns3, 'ip' => @$server->ns3_ip],
            ['label' => 'NS4', 'host' => @$server->ns4, 'ip' => @$server->ns4_ip],
        ])->filter(fn ($record) => !empty($record['host']))->values();

        return view('Template::user.service.details', compact('pageTitle', 'service', 'accountSummary', 'serverGroup', 'execute', 'cancelRequestTypes', 'diskUsagePercent', 'product', 'status', 'hasAccount', 'nameservers', 'zodPanelDiagnostics'));
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

        $execute = $this->zodPanelAction($service, 'repairWebmail', [
            'create_mail_domain' => true,
        ]);

        $notify[] = [@$execute['success'] ? 'success' : 'error', @$execute['message'] ?: 'ZodPanel webmail repair completed'];
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
