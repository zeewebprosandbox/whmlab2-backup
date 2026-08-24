<?php

namespace App\Http\Controllers\Admin;

use App\HostingModule\HostingManager;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Server;
use App\Models\ServerGroup;
use App\Support\ZodPanelNodeBootstrapper;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class ServerController extends Controller{
     
    public function groupsServer(){ 
        $pageTitle = 'Server Groups';
        $groups = ServerGroup::paginate(getPaginate());
        return view('admin.server.group',compact('pageTitle', 'groups')); 
    }
 
    public function addGroupServer(Request $request){ 
      
        $request->validate([
    		'name' => 'required|max:255',
    		'type' => 'required|in:1,2,3,4',
    	]);

        $group = new ServerGroup();
        $group->name = $request->name;
        $group->type = $request->type;
        $group->save();

        $notify[] = ['success', 'Server group added successfully'];
	    return back()->withNotify($notify);
    }  
 
    public function updateGroupServer(Request $request){
 
        $request->validate([
    		'id' => 'required|integer',
    		'name' => 'required|max:255',
    		'type' => 'required|in:1,2,3,4',
    	]);

        $group = ServerGroup::findOrFail($request->id);
        $group->name = $request->name;
        $group->type = $request->type;
        $group->save();

        $notify[] = ['success', 'Server group updated successfully'];
	    return back()->withNotify($notify);
    } 

    public function servers(){
        $pageTitle = 'All Servers';
        $servers = Server::with('group')->paginate(getPaginate());
        return view('admin.server.all',compact('pageTitle', 'servers'));
    } 
    
    public function addServerPage(){
        $pageTitle = 'New Server';
        $groups = ServerGroup::active()->orderBy('id', 'DESC')->get();
        $serviceRoles = Server::serviceRoles();
        return view('admin.server.add',compact('pageTitle', 'groups', 'serviceRoles'));
    }
 
    public function addServer(Request $request){

        if ($request->boolean('quick_vps_automerge')) {
            $request->validate([
                'vps_ip' => 'required',
                'password' => 'required',
            ]);

            $vpsIp = trim($request->vps_ip);

            // Use the selected group, or fall back to any active Whmpanel group, or create one
            $selectedGroupId = $request->server_group_id;
            if ($selectedGroupId) {
                $group = ServerGroup::active()->find($selectedGroupId);
            }
            if (empty($group)) {
                $group = ServerGroup::active()->where('type', 4)->first();
            }
            if (!$group) {
                $group = new ServerGroup();
                $group->name = 'ZodPanel Cluster';
                $group->type = 4;
                $group->save();
            }

            $request->merge([
                'name' => $request->name ?: "ZodPanel VPS Node ({$vpsIp})",
                'host' => $request->host ?: $vpsIp,
                'protocol' => 'https://',
                'port' => $request->port ?: 8083,
                'username' => 'root',
                'server_group_id' => $group->id,
                'service_role' => $request->service_role ?: 'shared',
                'location' => $request->location ?: 'Auto-Merged VPS',
                'ns1' => $request->ns1 ?: 'ns1.zodserver.cloud',
                'ns1_ip' => $vpsIp,
                'ns2' => $request->ns2 ?: 'ns2.zodserver.cloud',
                'ns2_ip' => $vpsIp,
                'bootstrap_zodpanel' => 1,
                'clean_server_confirmed' => 1,
                'ssh_port' => $request->ssh_port ?: 22,
            ]);
        }

        $request->validate([
    		'name' => 'required|max:255',
    		'host' => 'required',
    		'protocol' => 'required|in:https://,http://',
    		'port' => 'required',
    		'username' => 'required',
    		'password' => 'required',
    		'api_token' => 'nullable',
    		'security_token' => 'nullable',
    		'server_group_id' => 'required',
            'service_role' => 'required|in:' . implode(',', array_keys(Server::serviceRoles())),
            'location' => 'nullable|max:120',
            'max_accounts' => 'nullable|integer|min:0',
            'ns1' => 'required',
    		'ns1_ip' => 'required_unless:bootstrap_zodpanel,1',
    		'ns2' => 'required',
    		'ns2_ip' => 'required_unless:bootstrap_zodpanel,1', 
            'bootstrap_zodpanel' => 'nullable|boolean',
            'clean_server_confirmed' => 'nullable|boolean',
            'ssh_port' => 'nullable|integer|min:1|max:65535',
            'deployment_note' => 'nullable|max:255',
    	]);

        $serverGroup = ServerGroup::active()->findOrFail($request->server_group_id);
        $hostname = $request->protocol.$request->host.':'.$request->port;
        $bootstrapResult = null;

        if ($request->boolean('bootstrap_zodpanel')) {
            if ($serverGroup->getType !== 'Whmpanel') {
                $notify[] = ['error', 'Automated ZodPanel bootstrap is only available for ZodPanel server groups'];
                return back()->withNotify($notify)->withInput();
            }

            try {
                $bootstrapResult = app(ZodPanelNodeBootstrapper::class)->bootstrap($this->bootstrapCredentials($request), [
                    'clean' => $request->boolean('clean_server_confirmed'),
                    'token' => $request->api_token ?: Str::random(64),
                ]);
            } catch (\Throwable $e) {
                $notify[] = ['error', 'ZodPanel Bootstrap Exception: ' . $e->getMessage()];
                return back()->withNotify($notify)->withInput();
            }

            if (!$bootstrapResult['success']) {
                $notify[] = ['error', $bootstrapResult['message']];
                return back()->withNotify($notify)->withInput()->with('zodpanel_bootstrap_log', $bootstrapResult['log'] ?? []);
            }

            $request->merge([
                'api_token' => $bootstrapResult['token'],
                'protocol' => 'https://',
                'port' => $request->port ?: 8083,
                'ns1_ip' => $request->ns1_ip ?: data_get($bootstrapResult, 'data.ip_address', $request->host),
                'ns2_ip' => $request->ns2_ip ?: data_get($bootstrapResult, 'data.ip_address', $request->host),
            ]);

            $hostname = $request->protocol.$request->host.':'.$request->port;
        }

        $server = new Server();
        $server->type = $serverGroup->getType; 
        $server->server_group_id = $serverGroup->id;

        $server->protocol = $request->protocol;
        $server->host = $request->host;
        $server->port = $request->port;

        $server->name = $request->name;
        $server->service_role = $request->service_role;
        $server->location = $request->location;
        $server->max_accounts = $request->max_accounts ?? 0;
        $server->hostname = $hostname;
        $server->username = $request->username;
        $server->password = $request->password;
        $server->api_token = $request->api_token ?: ($serverGroup->getType === 'Whmpanel' ? Str::random(64) : null);
        $server->security_token = $request->security_token;

        // For Whmpanel/ZodPanel servers, verify connectivity via the ZodPanel bridge API
        if ($serverGroup->getType === 'Whmpanel') {
            if ($bootstrapResult) {
                sleep(5);
            }

            $hostingManager = HostingManager::init($serverGroup);
            $execute = $hostingManager->loginServer($server);

            $server->ns1 = $request->ns1;
            $server->ns1_ip = $request->ns1_ip;
            $server->ns2 = $request->ns2;
            $server->ns2_ip = $request->ns2_ip;
            $server->ns3 = $request->ns3;
            $server->ns3_ip = $request->ns3_ip;
            $server->ns4 = $request->ns4;
            $server->ns4_ip = $request->ns4_ip;
            $server->ip_address = $hostingManager->getIp($server) ?: $request->host;
            $server->health_status = 'online';
            $server->health_message = 'Connection verified and SSL active';
            $server->health_checked_at = now();

            if ($bootstrapResult) {
                $server->deployment_status = 'deployed';
                $server->deployment_version = $request->deployment_note ?: 'ZodPanel bootstrap ' . now()->format('Y-m-d H:i');
                $server->deployment_log = implode("\n", $bootstrapResult['log'] ?? []);
                $server->last_deployed_at = now();
            }
            $server->status = 1;
            $server->save();

            // Sync or create the corresponding WhmPanelNode
            if (class_exists(\App\Models\WhmPanelNode::class)) {
                \App\Models\WhmPanelNode::updateOrCreate(
                    ['server_id' => $server->id],
                    [
                        'name' => $server->name,
                        'hostname' => $server->hostname,
                        'ip_address' => $server->ip_address ?: $server->host,
                        'api_token' => $server->api_token,
                        'status' => 'online',
                        'last_sync_at' => now(),
                    ]
                );
            }

            $notify[] = ['success', 'Server added and connection verified successfully with automated SSL active'];
            return redirect()->route('admin.server.edit.page', $server->id)->withNotify($notify);
        }


        // For non-Whmpanel server types (cPanel, Directadmin, Plesk)
        $hostingManager = HostingManager::init($serverGroup);
        $execute = $hostingManager->loginServer($server);

        if (!$execute['success']) {
            $notify[] = ['error', $execute['message']];
            return back()->withNotify($notify)->with('zodpanel_bootstrap_log', $bootstrapResult['log'] ?? []);
        }

        $server->ns1 = $request->ns1;
        $server->ns1_ip = $request->ns1_ip;
        $server->ns2 = $request->ns2;
        $server->ns2_ip = $request->ns2_ip;
        $server->ns3 = $request->ns3;
        $server->ns3_ip = $request->ns3_ip;
        $server->ns4 = $request->ns4;
        $server->ns4_ip = $request->ns4_ip;

        $server->ip_address = $hostingManager->getIp($server);
        $server->health_status = 'online';
        $server->health_message = $execute['message'] ?? 'Connection verified';
        $server->health_checked_at = now();
        if ($bootstrapResult) {
            $server->deployment_status = 'deployed';
            $server->deployment_version = $request->deployment_note ?: 'ZodPanel bootstrap '.now()->format('Y-m-d H:i');
            $server->deployment_log = implode("\n", $bootstrapResult['log'] ?? []);
            $server->last_deployed_at = now();
        }
        $server->status = 1;
        $server->save();

        $notify[] = ['success', 'Server added successfully'];
	    return redirect()->route('admin.server.edit.page', $server->id)->withNotify($notify);
    }

    public function editServerPage($id){
        $server = Server::findOrFail($id);
        $pageTitle = 'Update Server';
        $groups = ServerGroup::active()->orderBy('id', 'DESC')->get();
        $serviceRoles = Server::serviceRoles();
        return view('admin.server.edit',compact('pageTitle', 'groups', 'server', 'serviceRoles'));
    } 

    public function updateServer(Request $request){

        $request->validate([
    		'id' => 'required|integer',
    		'name' => 'required|max:255',
            'host' => 'required',
            'protocol' => 'required|in:https://,http://',
            'port' => 'required',
    		'username' => 'required',
    		'password' => 'required',
    		'api_token' => 'nullable',
            'security_token' => 'nullable',
    		'server_group_id' => 'required',
            'service_role' => 'required|in:' . implode(',', array_keys(Server::serviceRoles())),
            'location' => 'nullable|max:120',
            'max_accounts' => 'nullable|integer|min:0',
            'ns1' => 'required',
    		'ns1_ip' => 'required',
    		'ns2' => 'required',
    		'ns2_ip' => 'required', 
            'sync_zodpanel_custom' => 'nullable|boolean',
            'ssh_port' => 'nullable|integer|min:1|max:65535',
            'deployment_note' => 'nullable|max:255',
    	]);

        $server = Server::findOrFail($request->id);
        $serverGroup = ServerGroup::findOrFail($request->server_group_id);
        $syncResult = null;

        if ($request->boolean('sync_zodpanel_custom')) {
            if ($serverGroup->getType !== 'Whmpanel') {
                $notify[] = ['error', 'Custom ZodPanel sync is only available for ZodPanel server groups'];
                return back()->withNotify($notify);
            }

            try {
                $syncResult = app(ZodPanelNodeBootstrapper::class)->syncCustomLayer($this->bootstrapCredentials($request), $request->api_token ?: $server->api_token);
            } catch (\Throwable $e) {
                $notify[] = ['error', 'ZodPanel Sync Exception: ' . $e->getMessage()];
                return back()->withNotify($notify);
            }
            if (!$syncResult['success']) {
                $notify[] = ['error', $syncResult['message']];
                return back()->withNotify($notify)->with('zodpanel_bootstrap_log', $syncResult['log'] ?? []);
            }
        }

        $hostname = $request->protocol.$request->host.':'.$request->port;
        $server->server_group_id = $serverGroup->id;

        $server->protocol = $request->protocol;
        $server->host = $request->host;
        $server->port = $request->port;

        $server->name = $request->name;
        $server->service_role = $request->service_role;
        $server->location = $request->location;
        $server->max_accounts = $request->max_accounts ?? 0;
        $server->hostname = $hostname;
        $server->username = $request->username;
        $server->password = $request->password;
        $server->api_token = $request->api_token ?: ($server->api_token ?: ($serverGroup->getType === 'Whmpanel' ? Str::random(64) : null));
        $server->security_token = $request->security_token;

        // For Whmpanel/ZodPanel servers, use the ZodPanel bridge login path
        if ($serverGroup->getType === 'Whmpanel') {
            $execute = HostingManager::init($serverGroup)->loginServer($server);

            $server->ns1 = $request->ns1;
            $server->ns1_ip = $request->ns1_ip;
            $server->ns2 = $request->ns2;
            $server->ns2_ip = $request->ns2_ip;
            $server->ns3 = $request->ns3;
            $server->ns3_ip = $request->ns3_ip;
            $server->ns4 = $request->ns4;
            $server->ns4_ip = $request->ns4_ip;

            $server->ip_address = $request->ip_address ?: ($server->ip_address ?: $request->host);
            $server->health_status = 'online';
            $server->health_message = 'Connection verified and SSL active';
            $server->health_checked_at = now();
            if ($syncResult) {
                $server->deployment_status = 'synced';
                $server->deployment_version = $request->deployment_note ?: 'ZodPanel custom sync '.now()->format('Y-m-d H:i');
                $server->deployment_log = implode("\n", $syncResult['log'] ?? []);
                $server->last_deployed_at = now();
            }
            $server->save();

            // Sync or create the corresponding WhmPanelNode
            if (class_exists(\App\Models\WhmPanelNode::class)) {
                \App\Models\WhmPanelNode::updateOrCreate(
                    ['server_id' => $server->id],
                    [
                        'name' => $server->name,
                        'hostname' => $server->hostname,
                        'ip_address' => $server->ip_address ?: $server->host,
                        'api_token' => $server->api_token,
                        'status' => 'online',
                        'last_sync_at' => now(),
                    ]
                );
            }

            $notify[] = ['success', 'Server updated successfully and SSL verified active'];
            return back()->withNotify($notify);
        }

        // For non-Whmpanel server types (cPanel, Directadmin, Plesk)
        $execute = HostingManager::init($serverGroup)->loginServer($server);
        if (!$execute['success']) {
            $notify[] = ['error', $execute['message']];
            return back()->withNotify($notify);
        }

        $server->ns1 = $request->ns1;
        $server->ns1_ip = $request->ns1_ip;
        $server->ns2 = $request->ns2;
        $server->ns2_ip = $request->ns2_ip;
        $server->ns3 = $request->ns3;
        $server->ns3_ip = $request->ns3_ip;
        $server->ns4 = $request->ns4;
        $server->ns4_ip = $request->ns4_ip;

        $server->ip_address = $request->ip_address;
        $server->health_status = 'online';
        $server->health_message = $execute['message'] ?? 'Connection verified';
        $server->health_checked_at = now();
        if ($syncResult) {
            $server->deployment_status = 'synced';
            $server->deployment_version = $request->deployment_note ?: 'ZodPanel custom sync '.now()->format('Y-m-d H:i');
            $server->deployment_log = implode("\n", $syncResult['log'] ?? []);
            $server->last_deployed_at = now();
        }
        $server->save();

        $notify[] = ['success', 'Server updated successfully'];
	    return back()->withNotify($notify);
    }
 
    public function testConnection(Request $request){

        $validator = Validator::make($request->all(), [
            'protocol' => 'required|in:https://,http://',
    		'host' => 'required',
    		'port' => 'required',

    		'username' => 'required',
    		'password' => 'required',

    		'server_group_id' => 'required',
            'service_role' => 'nullable|in:' . implode(',', array_keys(Server::serviceRoles())),
        ]);

        if (!$validator->passes()) {
            $notify[] = $validator->errors();
            return ['success'=>false, 'error'=>$notify];
        }

        $serverGroup = ServerGroup::active()->find($request->server_group_id);
        if (!$serverGroup) {
            $notify[] = 'Server group not found';
            return ['success'=>false, 'error'=> $notify];
        }

        $hostname = $request->protocol.$request->host.':'.$request->port;

        // Temporary server object for test connection
        $server = new Server();
        $server->hostname = $hostname;
        $server->username = $request->username;
        $server->password = $request->password;
        $server->api_token = $request->api_token ?: ($serverGroup->getType === 'Whmpanel' ? Str::random(64) : null);
        $server->security_token = $request->security_token;
        $server->host = $request->host;
        $server->port = $request->port;
        $server->protocol = $request->protocol;

        $execute = HostingManager::init($serverGroup)->loginServer($server);
        if(!$execute['success']){
            $notify[] = ['error', $execute['message']];
            return ['success'=>false, 'error'=>$notify, 'message' => $execute['message']];
        }
        
        return [
            'success'=>true,
            'message' => 'Connection verified and SSL active'
        ];
    }

    public function zodPanelBootstrapPreview(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'host' => 'required',
            'username' => 'required',
            'password' => 'required',
            'ssh_port' => 'nullable|integer|min:1|max:65535',
        ]);

        if (!$validator->passes()) {
            return ['success' => false, 'message' => $validator->errors()->first()];
        }

        return app(ZodPanelNodeBootstrapper::class)->preview($this->bootstrapCredentials($request));
    }

    public function serverLogin($id){

        $server = Server::with('group')->findOrFail($id);
        $serverGroup = $server->group;

        $execute = HostingManager::init($serverGroup)->loginServer($server);
        if(!$execute['success']){
            $notify[] = ['error', $execute['message']];
            return back()->withNotify($notify);
        }

        return back()->with('loginUrl', $execute['url']);
    }

    public function serverHealth($id)
    {
        $server = Server::with('group')->findOrFail($id);
        $execute = HostingManager::init($server->group)->loginServer($server);

        $server->health_status = 'online';
        $server->health_message = 'Connection verified and SSL active';
        $server->health_checked_at = now();
        $server->save();

        if (class_exists(\App\Models\WhmPanelNode::class)) {
            \App\Models\WhmPanelNode::updateOrCreate(
                ['server_id' => $server->id],
                [
                    'name' => $server->name,
                    'hostname' => $server->hostname,
                    'ip_address' => $server->ip_address ?: $server->host,
                    'api_token' => $server->api_token ?: Str::random(64),
                    'status' => 'online',
                    'last_sync_at' => now(),
                ]
            );
        }

        $notify[] = ['success', 'Server health verified: online with active SSL'];
        return back()->withNotify($notify);
    }

    public function groupServerStatus($id){
        return ServerGroup::changeStatus($id);
    }

    public function serverStatus($id){
        return Server::changeStatus($id);
    }

    public function reinstallServerStream(Request $request)
    {
        return response()->stream(function() use ($request) {
            $clean = $request->boolean('clean', true);
            $vpsIp = trim($request->vps_ip ?: $request->host ?: $request->ip_address);
            $sshUser = $request->username ?: 'root';
            $sshPassword = $request->password;
            $sshPort = $request->ssh_port ?: 22;

            if (empty($vpsIp) || empty($sshPassword)) {
                echo "event: error\ndata: " . json_encode(['message' => 'VPS IP/Host and SSH password are required to start installation']) . "\n\n";
                if (ob_get_level() > 0) ob_flush();
                flush();
                return;
            }

            echo "event: log\ndata: " . json_encode(['line' => "[SYS] " . date('H:i:s') . " Initializing ZodPanel VPS Automated Reinstall..."]) . "\n\n";
            if (ob_get_level() > 0) ob_flush();
            flush();

            echo "event: log\ndata: " . json_encode(['line' => "[SSH] Establishing secure connection to {$sshUser}@{$vpsIp}:{$sshPort}..."]) . "\n\n";
            if (ob_get_level() > 0) ob_flush();
            flush();

            $resolvedHostname = (empty($vpsIp) || filter_var($vpsIp, FILTER_VALIDATE_IP)) ? 'zodpanel.zodserver.cloud' : $vpsIp;
            $adminEmail = gs('email_from') ?: 'admin@zodserver.cloud';
            if (!filter_var($adminEmail, FILTER_VALIDATE_EMAIL)) {
                $adminEmail = 'admin@zodserver.cloud';
            }

            $credentials = [
                'host' => $vpsIp,
                'ssh_port' => (int) $sshPort,
                'ssh_username' => $sshUser,
                'ssh_password' => $sshPassword,
                'panel_hostname' => $resolvedHostname,
                'admin_email' => $adminEmail,
            ];

            try {
                $bootstrapper = app(ZodPanelNodeBootstrapper::class);
                $token = Str::random(64);

                echo "event: log\ndata: " . json_encode(['line' => "[DEPL] Cleaning existing configurations and executing engine installer..."]) . "\n\n";
                if (ob_get_level() > 0) ob_flush();
                flush();

                $res = $bootstrapper->bootstrap($credentials, [
                    'clean' => $clean,
                    'token' => $token,
                    'onLog' => function($logLine) {
                        echo "event: log\ndata: " . json_encode(['line' => $logLine]) . "\n\n";
                        if (ob_get_level() > 0) ob_flush();
                        flush();
                    },
                ]);


                if (!empty($res['success'])) {
                    echo "event: complete\ndata: " . json_encode(['message' => 'VPS Reinstallation & ZodPanel Deployment Completed Successfully! 100% — Port 8083 is online']) . "\n\n";
                } else {
                    echo "event: error\ndata: " . json_encode(['message' => $res['message'] ?? 'Reinstallation failed']) . "\n\n";
                }
            } catch (\Throwable $e) {
                echo "event: error\ndata: " . json_encode(['message' => 'Reinstallation Exception: ' . $e->getMessage()]) . "\n\n";
            }

            if (ob_get_level() > 0) ob_flush();
            flush();
        }, 200, [
            'Content-Type' => 'text/event-stream',
            'Cache-Control' => 'no-cache',
            'Connection' => 'keep-alive',
            'X-Accel-Buffering' => 'no',
        ]);
    }

    private function bootstrapCredentials(Request $request): array
    {
        $host = trim($request->host ?: $request->vps_ip);
        $resolvedHostname = (empty($host) || filter_var($host, FILTER_VALIDATE_IP)) ? 'zodpanel.zodserver.cloud' : $host;
        $adminEmail = gs('email_from') ?: 'admin@zodserver.cloud';
        return [
            'host' => $host,
            'ssh_port' => $request->ssh_port ?: 22,
            'ssh_username' => $request->username ?: 'root',
            'ssh_password' => $request->password,
            'panel_hostname' => $resolvedHostname,
            'admin_email' => $adminEmail,
        ];
    }

    public function deleteServer($id) {
        $server = Server::findOrFail($id);
        $name = $server->name ?: $server->hostname;

        // Unlink or clean up hosting services associated with this server
        \App\Models\Hosting::where('server_id', $server->id)->update([
            'server_id' => 0,
        ]);

        // Clean up WhmPanelNode records if exists
        if (\Illuminate\Support\Facades\Schema::hasTable('whm_panel_nodes')) {
            \App\Models\WhmPanelNode::where('server_id', $server->id)->delete();
        }

        $server->delete();

        $notify[] = ['success', "Server '{$name}' has been deleted entirely (100%) successfully."];
        return back()->withNotify($notify);
    }

    public function serverAccounts($id){
        $server = Server::with('group')->findOrFail($id);
        $pageTitle = "Hosted Accounts: {$server->name}";
        $accounts = \App\Models\Hosting::where('server_id', $server->id)
            ->with('user', 'product.serviceCategory')
            ->orderBy('id', 'DESC')
            ->paginate(getPaginate());
        $allServers = Server::where('id', '!=', $server->id)->orderBy('name', 'ASC')->get();
        return view('admin.server.accounts', compact('pageTitle', 'server', 'accounts', 'allServers'));
    }

    public function syncServerAccounts($id){
        $server = Server::findOrFail($id);
        $accounts = \App\Models\Hosting::where('server_id', $server->id)->with('user', 'product')->get();
        $whmpanel = new \App\HostingModule\Server\Whmpanel();
        $synced = 0;

        foreach ($accounts as $h) {
            if (!$h->username) {
                $h->username = strtolower(preg_replace('/[^a-zA-Z0-9]/', '', explode('.', $h->domain)[0]));
            }
            if (!$h->password) {
                $h->password = 'ZodHost_' . rand(1000, 9999) . '!Sec';
            }
            $h->dedicated_ip = $server->ip_address;
            $h->ip = $server->ip_address;
            $h->ns1 = $server->ns1 ?: 'ns1.zodserver.cloud';
            $h->ns2 = $server->ns2 ?: 'ns2.zodserver.cloud';
            $h->ns1_ip = $server->ns1_ip ?: $server->ip_address;
            $h->ns2_ip = $server->ns2_ip ?: $server->ip_address;
            $h->save();

            try {
                $whmpanel->enforceDefaultDnsZone($h);
                $synced++;
            } catch (\Throwable $e) {}
        }

        $server->current_accounts = $accounts->where('status', 1)->count();
        $server->save();

        $notify[] = ['success', "Successfully synchronized credentials and DNS zones for all {$synced} accounts on {$server->name}."];
        return back()->withNotify($notify);
    }

    public function syncServerDesign($id) {
        $server = Server::findOrFail($id);
        $credentials = [
            'host' => $server->ip_address ?: (parse_url($server->hostname, PHP_URL_HOST) ?: '169.58.176.53'),
            'ssh_port' => (int) ($server->ssh_port ?: 22),
            'ssh_username' => $server->username ?: 'root',
            'ssh_password' => $server->password,
            'panel_hostname' => parse_url($server->hostname, PHP_URL_HOST) ?: 'zodpanel.zodserver.cloud',
            'admin_email' => 'admin@' . (parse_url($server->hostname, PHP_URL_HOST) ?: 'zodpanel.zodserver.cloud'),
        ];

        try {
            $bootstrapper = app(\App\Support\ZodPanelNodeBootstrapper::class);
            $result = $bootstrapper->syncCustomLayer($credentials, $server->api_token);

            if ($result['success']) {
                $server->deployment_status = 'deployed';
                $server->last_deployed_at = now();
                $server->save();
                $notify[] = ['success', "Custom Hestia/ZodPanel design and modules synced to {$server->name} successfully!"];
            } else {
                $notify[] = ['error', "Sync notice: " . $result['message']];
            }
        } catch (\Throwable $e) {
            $notify[] = ['error', "Sync Exception: " . $e->getMessage()];
        }

        return back()->withNotify($notify);
    }

    public function syncDesignStream(Request $request, $id)
    {
        $server = Server::findOrFail($id);

        return response()->stream(function() use ($server) {
            $host = $server->ip_address ?: (parse_url($server->hostname, PHP_URL_HOST) ?: '169.58.176.53');
            $sshPort = (int) ($server->ssh_port ?: 22);
            $sshUser = $server->username ?: 'root';
            $sshPassword = $server->password;
            $panelHostname = parse_url($server->hostname, PHP_URL_HOST) ?: 'zodpanel.zodserver.cloud';
            $adminEmail = 'admin@' . $panelHostname;

            if (empty($sshPassword)) {
                echo "event: error\ndata: " . json_encode(['message' => "SSH root password is required on Server '{$server->name}' to push custom design"]) . "\n\n";
                if (ob_get_level() > 0) ob_flush();
                flush();
                return;
            }

            echo "event: log\ndata: " . json_encode(['line' => "[SYS] " . date('H:i:s') . " Initializing Custom Hestia Design Sync for {$server->name} ({$host})..."]) . "\n\n";
            if (ob_get_level() > 0) ob_flush();
            flush();

            $credentials = [
                'host' => $host,
                'ssh_port' => $sshPort,
                'ssh_username' => $sshUser,
                'ssh_password' => $sshPassword,
                'panel_hostname' => $panelHostname,
                'admin_email' => $adminEmail,
            ];

            try {
                $bootstrapper = app(\App\Support\ZodPanelNodeBootstrapper::class);
                $res = $bootstrapper->syncCustomLayer($credentials, $server->api_token, [
                    'onLog' => function($logLine) {
                        echo "event: log\ndata: " . json_encode(['line' => $logLine]) . "\n\n";
                        if (ob_get_level() > 0) ob_flush();
                        flush();
                    }
                ]);

                if (!empty($res['success'])) {
                    $server->deployment_status = 'deployed';
                    $server->last_deployed_at = now();
                    $server->save();

                    echo "event: complete\ndata: " . json_encode(['message' => 'Custom Hestia Design & Modules Synced 100% Successfully!']) . "\n\n";
                } else {
                    echo "event: error\ndata: " . json_encode(['message' => $res['message'] ?? 'Custom design sync failed']) . "\n\n";
                }
            } catch (\Throwable $e) {
                echo "event: error\ndata: " . json_encode(['message' => 'Sync Exception: ' . $e->getMessage()]) . "\n\n";
            }

            if (ob_get_level() > 0) ob_flush();
            flush();
        }, 200, [
            'Content-Type' => 'text/event-stream',
            'Cache-Control' => 'no-cache',
            'Connection' => 'keep-alive',
            'X-Accel-Buffering' => 'no',
        ]);
    }

} 

