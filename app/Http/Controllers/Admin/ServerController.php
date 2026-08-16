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
            $group = ServerGroup::active()->where('type', 2)->first() ?? ServerGroup::first();
            if (!$group) {
                $group = new ServerGroup();
                $group->name = 'ZodPanel Cluster';
                $group->type = 2;
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
        $server->api_token = $request->api_token;
        $server->security_token = $request->security_token;

        $hostingManager = HostingManager::init($serverGroup);
        $execute = $hostingManager->loginServer($server);

        if(!$execute['success']){
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
        $server->api_token = $request->api_token;
        $server->security_token = $request->security_token;

        $execute = HostingManager::init($serverGroup)->loginServer($server);
        if(!$execute['success']){
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
        $server->api_token = $request->api_token;
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
            'success'=>true
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

        $server->health_status = $execute['success'] ? 'online' : 'offline';
        $server->health_message = $execute['message'] ?? ($execute['success'] ? 'Connection verified' : 'Connection failed');
        $server->health_checked_at = now();
        $server->save();

        $notify[] = [$execute['success'] ? 'success' : 'error', $server->health_message];
        return back()->withNotify($notify);
    }

    public function groupServerStatus($id){
        return ServerGroup::changeStatus($id);
    }

    public function serverStatus($id){
        return Server::changeStatus($id);
    }

    private function bootstrapCredentials(Request $request): array
    {
        return [
            'host' => $request->host,
            'ssh_port' => $request->ssh_port ?: 22,
            'ssh_username' => $request->username ?: 'root',
            'ssh_password' => $request->password,
            'panel_hostname' => $request->host,
            'admin_email' => gs('email_from') ?: 'admin@'.$request->host,
        ];
    }

} 
