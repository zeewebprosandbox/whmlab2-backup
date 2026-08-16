<?php

namespace App\HostingModule\Server;

use App\Models\AdminNotification;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\ConnectionException;
use App\HostingModule\Server\HostingManagerInterface;

class Cpanel implements HostingManagerInterface{

    public function create($hosting){
        
        try{
            $user = $hosting->user;
            $product = $hosting->product; 
            $server = $hosting->server;

            $createPayload = [
                'api.version' => 1,
                'username' => $hosting->username,
                'domain' => $hosting->domain,
                'contactemail' => $user->email,
                'password' => $hosting->password,
                'pkgname' => $product->package_name,
            ];

            $response = $this->whmRequest($server, 'createacct', $createPayload);
    
            $response = json_decode($response);
            $responseStatus = $this->whmResponseStatus($response);
	     
            if(!@$responseStatus['success']){
                $message = @$responseStatus['message'];

                if ($this->isExistingDomainError((string) $message)) {
                    $replace = $this->replaceExistingDomainAccount($server, $hosting->domain);

                    if (!$replace['success']) {
                        $message = $replace['message'];
                    } else {
                        sleep(2);

                        $retry = $this->createAccountWithRetry($server, $createPayload);
                        $response = $retry['response'];
                        $responseStatus = $retry['status'];

                        if (!@$responseStatus['success']) {
                            $message = 'Old cPanel account was removed, but creating the new account still failed: ' . (@$responseStatus['message'] ?: 'Unknown WHM error');
                        }
                    }
                }

                if (@$responseStatus['success']) {
                    return $this->saveCreatedAccount($hosting, $product, $response);
                }

                $this->adminNotification($hosting, @$message);

                return [
                    'success'=>false, 
                    'message'=>@$message
                ];
            }

            return $this->saveCreatedAccount($hosting, $product, $response);

        }catch(\Exception  $error){
            return [
                'success'=>false, 
                'message'=>$error->getMessage()
            ];
        }
    }  

    private function createAccountWithRetry($server, array $payload): array
    {
        $response = null;
        $status = ['success' => false, 'message' => 'Unknown WHM error'];

        for ($attempt = 1; $attempt <= 2; $attempt++) {
            $response = json_decode($this->whmRequest($server, 'createacct', $payload));
            $status = $this->whmResponseStatus($response);

            if (@$status['success'] || !$this->isExistingDomainError((string) @$status['message'])) {
                break;
            }

            sleep(3);
        }

        return [
            'response' => $response,
            'status' => $status,
        ];
    }

    private function saveCreatedAccount($hosting, $product, $response): array
    {
        $hosting->ns1 = @$response->data->nameserver;
        $hosting->ns2 = @$response->data->nameserver2;
        $hosting->ns3 = @$response->data->nameserver3;
        $hosting->ns4 = @$response->data->nameserver4;
        $hosting->package_name = $product->package_name;
        $hosting->ip = @$response->data->ip;
        $hosting->save();

        return [
            'success'=>true,
            'message'=>$response
        ];
    }

    public function suspend($data){
        
        try{
            $hosting = $data['hosting'];
            $server = $hosting->server;
            $request = $data['request'];

            $response = $this->whmRequest($server, 'suspendacct', [
                'api.version' => 1,
                'user' => $hosting->username,
                'reason' => $request->suspend_reason,
            ]);
 
            $response = json_decode($response);
            $responseStatus = $this->whmResponseStatus($response);
 
            if(!@$responseStatus['success']){
                $message = @$responseStatus['message'];

                $this->adminNotification($hosting, @$message);

                return [
                    'success'=>false, 
                    'message'=>@$message
                ];
            }

            $hosting->suspend_reason = $request->suspend_reason;
            $hosting->suspend_date = now();
            $hosting->save();

            return [
                'success'=>true, 
            ];

        }catch(\Exception  $error){
            return [
                'success'=>false, 
                'message'=>$error->getMessage()
            ];
        }
    }

    public function unSuspend($hosting){

        try{
            $server = $hosting->server;

            $response = $this->whmRequest($server, 'unsuspendacct', [
                'api.version' => 1,
                'user' => $hosting->username,
            ]);
 
            $response = json_decode($response);
            $responseStatus = $this->whmResponseStatus($response);
 
            if(!@$responseStatus['success']){
                $message = @$responseStatus['message'];

                $this->adminNotification($hosting, @$message);

                return [
                    'success'=>false, 
                    'message'=>@$message
                ];
            }
            
            $hosting->suspend_reason = null;
            $hosting->suspend_date= null;
            $hosting->save();

            return [
                'success'=>true
            ];

        }catch(\Exception  $error){
            return [
                'success'=>false, 
                'message'=>$error->getMessage()
            ];
        }
    }

    public function terminate($hosting){
        
        try{
            $server = $hosting->server;
     
            $response = $this->whmRequest($server, 'removeacct', [
                'api.version' => 1,
                'user' => $hosting->username,
                'username' => $hosting->username,
            ]);
 
            $response = json_decode($response);
            $responseStatus = $this->whmResponseStatus($response);
   
            if(!@$responseStatus['success']){
                $message = @$responseStatus['message'];

                $this->adminNotification($hosting, @$message);

                return [
                    'success'=>false,
                    'message'=>@$message
                ];
            }

            $hosting->termination_date = now();
            $hosting->save();

            return [
                'success'=> true, 
                'message'=> 'Account terminated successfully'
            ];

        }catch(\Exception  $error){
            return [
                'success'=>false, 
                'message'=>$error->getMessage()
            ];
        }
    }

    public function changePackage($hosting){
        
        try{
            $server = $hosting->server;
            $product = $hosting->product;
            $token = 'WHM '.$server->username.':'.$server->api_token;

            $response = Http::withHeaders([
                'Authorization' => $token,
            ])->get($server->hostname.'/cpsess'.$server->security_token.'/json-api/changepackage?api.version=1&user='.$hosting->username.'&pkg='.$product->package_name);
 
            $response = json_decode($response);
            $responseStatus = $this->whmResponseStatus($response);
 
            if(!@$responseStatus['success']){
                $message = @$responseStatus['message'];

                $this->adminNotification($hosting, @$message);

                return [
                    'success'=>false, 
                    'message'=>@$message
                ];
            }

            $hosting->package_name = $product->package_name;
            $hosting->save();

            return [
                'success'=>true
            ];

        }catch(\Exception  $error){
            return [
                'success'=>false, 
                'message'=>$error->getMessage()
            ];
        }
    }

    public function changePassword($hosting){

        try{
            $server = $hosting->server;
            $token = 'WHM '.$server->username.':'.$server->api_token;

            $response = Http::withHeaders([
                'Authorization' => $token,
            ])->get($server->hostname.'/cpsess'.$server->security_token.'/json-api/passwd?api.version=1&user='.$hosting->username.'&password='.$hosting->password);
 
            $response = json_decode($response);
            $responseStatus = $this->whmResponseStatus($response);
 
            if(!@$responseStatus['success']){
                $message = @$responseStatus['message'];

                $this->adminNotification($hosting, @$message);

                return [
                    'success'=>false, 
                    'message'=>@$message
                ];
            }

            return [
                'success'=> true, 
                'message'=> 'Password changed successfully'
            ];

        }catch(\Exception  $error){
            return [
                'success'=>false, 
                'message'=>$error->getMessage()
            ];
        }
    }

    public function syncConfigOptions($hosting)
    {
        try {
            $server = $hosting->server;

            if (!$server || !$hosting->username) {
                return [
                    'success' => false,
                    'message' => 'Select a server and cPanel username before syncing configurable options',
                ];
            }

            $limits = $this->accountLimitsFromHosting($hosting);
            $response = $this->whmRequest($server, 'modifyacct', array_merge([
                'api.version' => 1,
                'user' => $hosting->username,
            ], $limits));

            $response = json_decode($response);
            $status = $this->whmResponseStatus($response);

            if (!$status['success']) {
                return [
                    'success' => false,
                    'message' => $status['message'] ?: 'cPanel configurable option sync failed',
                ];
            }

            return [
                'success' => true,
                'message' => 'cPanel configurable options synced',
            ];
        } catch (\Exception $error) {
            return [
                'success' => false,
                'message' => $error->getMessage(),
            ];
        }
    }

    public function accountSummary($hosting){

        try{
            $server = $hosting->server;

            $response = $this->whmRequest($server, 'accountsummary', [
                'api.version' => 1,
                'user' => $hosting->username,
            ]);
            
            $response = json_decode($response);
            $data = @$response->data->acct[0];
   
            return [
                'raw_data' => $data,
                'processed_data' => $this->getProcessedAccountSummary(@$response->data->acct[0]),
            ];

        }catch(\Exception  $error){ 
            Log::error($error->getMessage());
        }
    }

    public function loginServer($server){

        try{
            $token = 'Basic '.base64_encode($server->username.':'.$server->password);

            $response = Http::withHeaders([
                'Authorization' => $token,
            ])->get($server->hostname.'/json-api/create_user_session?api.version=1&user='.$server->username.'&service=whostmgrd');
    
            $response = json_decode($response);
           
            if(@$response->cpanelresult->error){
                $message = @$response->cpanelresult->data->reason;

                if($server->id){
                    $this->adminNotification(null, @$message, urlPath('admin.server.edit.page', $server->id));
                }

                return [
                    'success'=>false, 
                    'message'=>@$message
                ];
            }

            $redirectUrl = $response->data->url;
            return [
                'success'=>true, 
                'url'=>$redirectUrl
            ];

        }catch(\Exception  $error){
            return [
                'success'=>false, 
                'message'=>$error->getMessage()
            ];
        }
    }

    public function loginAccount($hosting){

        try{
            $server = $hosting->server;
            $token = 'Basic '.base64_encode($server->username.':'.$server->password);

            $response = Http::withHeaders([
                'Authorization' => $token,
            ])->get($server->hostname.'/json-api/create_user_session?api.version=1&user='.$hosting->username.'&service=cpaneld');
    
            $response = json_decode($response);
           
            if(@$response->cpanelresult->error || !@$response->metadata->result){
                $message = $response->cpanelresult->data->reason ?? @$response->metadata->reason;

                $this->adminNotification($hosting, @$message);

                return [
                    'success'=>false, 
                    'message'=>@$message
                ];
            }
          
            $redirectUrl = $response->data->url;
            return [
                'success'=>true, 
                'url'=>$redirectUrl
            ];

        }catch(\Exception  $error){
            return [
                'success'=>false, 
                'message'=>$error->getMessage()
            ];
        }
    }

    //Trying to get IP address from WHM API
    public function getIP($server){

        try{
            $token = 'WHM '.$server->username.':'.$server->api_token;

            $response = Http::withHeaders([
                'Authorization' => $token,
            ])->get($server->hostname.'/cpsess'.$server->security_token.'/json-api/accountsummary?api.version=1&user='.$server->username);

            $response = json_decode(@$response);
            return @$response->data->acct[0]->ip ?? null;
            
        }catch(\Exception  $error){
            Log::error($error->getMessage());
        }
    }
  
    public function getPackage($serverGroup){ 
        
        try{
            $packages = [];
            $servers = $serverGroup->servers()->active()->get();
            
            foreach($servers as $server){
                $serverPackages = $this->packagesForServer($server);

                if (!$serverPackages['success']) {
                    return $serverPackages;
                }

                $packages[$server->id] = $serverPackages['data'];
            }

            return [
                'success'=>true, 
                'data'=>$packages
            ]; 

        }catch(\Exception  $error){
            return [
                'success'=>false, 
                'message'=>$error->getMessage()
            ];
        }
    }

    public function createPackage($data)
    {
        try {
            $serverGroup = $data['server_group'];
            $products = $data['products'];
            $synced = [];
            $servers = $serverGroup->servers()->active()->get();

            foreach ($products as $product) {
                $basePackageName = $this->cpanelSafePackageName($product);
                $targetServer = $servers->first();

                foreach ($servers as $server) {
                    $packages = $this->packagesForServer($server);

                    if (!$packages['success']) {
                        return $packages;
                    }

                    $existingPackage = $this->matchingPackageName($packages['data'], $basePackageName);

                    if ($existingPackage) {
                        $synced[$product->id] = [
                            'server_id' => $server->id,
                            'package_name' => $existingPackage,
                            'status' => 'existing',
                        ];

                        continue 2;
                    }

                    $targetServer = $server;
                    break;
                }

                $create = $this->createPackageOnServer($targetServer, $product, $basePackageName);

                if (!$create['success']) {
                    return $create;
                }

                $synced[$product->id] = [
                    'server_id' => $targetServer->id,
                    'package_name' => $create['package_name'],
                    'status' => 'created',
                ];
            }

            return [
                'success' => true,
                'data' => $synced,
            ];
        } catch (\Exception $error) {
            return [
                'success' => false,
                'message' => $error->getMessage(),
            ];
        }
    }

    private function packagesForServer($server): array
    {
        $response = $this->whmRequest($server, 'listpkgs', ['api.version' => 1]);
        $response = json_decode($response);

        $status = $this->whmResponseStatus($response);

        if (!$status['success']) {
            return [
                'success' => false,
                'message' => $status['message'] ?: 'Unable to fetch packages from cPanel',
            ];
        }

        return [
            'success' => true,
            'data' => array_column(@$response->data->pkg ?? [], 'name'),
        ];
    }

    private function createPackageOnServer($server, $product, string $basePackageName): array
    {
        $limits = $this->packageLimitsFromProduct($product);

        $response = $this->whmRequest($server, 'addpkg', array_merge([
            'api.version' => 1,
            'name' => $basePackageName,
            'cgi' => 1,
            'hasshell' => 0,
        ], $limits));

        $response = json_decode($response);

        if (@$response->metadata->result == 0) {
            $reason = $this->cleanWhmReason(@$response->metadata->reason ?: 'Unable to create cPanel package');

            if (!str_contains(strtolower($reason), 'already exists')) {
                return [
                    'success' => false,
                    'message' => "Failed to create package {$basePackageName} on {$server->name}: {$reason}",
                ];
            }
        } elseif (!@$response->metadata && !@$response->data) {
            return [
                'success' => false,
                'message' => "Failed to create package {$basePackageName} on {$server->name}: Invalid cPanel response",
            ];
        }

        $packages = $this->packagesForServer($server);

        if (!$packages['success']) {
            return $packages;
        }

        return [
            'success' => true,
            'package_name' => $this->matchingPackageName($packages['data'], $basePackageName) ?: $basePackageName,
        ];
    }

    private function whmRequest($server, string $command, array $query)
    {
        $endpoint = rtrim($server->hostname, '/');

        if ($server->security_token) {
            $endpoint .= '/cpsess' . $server->security_token;
        }

        $endpoint .= '/json-api/' . ltrim($command, '/');

        $request = Http::connectTimeout(20)
            ->timeout(75)
            ->retry(1, 1000)
            ->withOptions($this->curlOptions());

        if ($server->api_token) {
            $request = $request->withHeaders([
                'Authorization' => 'WHM ' . $server->username . ':' . $server->api_token,
            ]);
        } elseif ($server->password) {
            $request = $request->withBasicAuth($server->username, $server->password);
        }

        try {
            return $request->get($endpoint, $query);
        } catch (ConnectionException $error) {
            return $this->failedWhmResponse(
                "Unable to connect to cPanel/WHM at {$endpoint}. {$this->cleanCurlMessage($error->getMessage())}"
            );
        } catch (\Exception $error) {
            return $this->failedWhmResponse(
                "cPanel/WHM request failed at {$endpoint}. {$this->cleanCurlMessage($error->getMessage())}"
            );
        }
    }

    private function replaceExistingDomainAccount($server, string $domain): array
    {
        $username = $this->findAccountUsernameByDomain($server, $domain);

        if (!$username) {
            return $this->removeExistingDnsZone($server, $domain);
        }

        $response = $this->whmRequest($server, 'removeacct', [
            'api.version' => 1,
            'user' => $username,
            'username' => $username,
            'keepdns' => 0,
        ]);

        $response = json_decode($response);
        $status = $this->whmResponseStatus($response);

        if (!$status['success']) {
            return [
                'success' => false,
                'message' => "The old cPanel account {$username} for {$domain} could not be removed: " . ($status['message'] ?: 'Unknown WHM error'),
            ];
        }

        $released = $this->waitForDomainRelease($server, $domain);

        if (!$released['success']) {
            return $released;
        }

        return [
            'success' => true,
            'username' => $username,
        ];
    }

    private function removeExistingDnsZone($server, string $domain): array
    {
        $response = $this->whmRequest($server, 'killdns', [
            'api.version' => 1,
            'domain' => $this->normalizeDomain($domain),
        ]);

        $response = json_decode($response);
        $status = $this->whmResponseStatus($response);

        if (!$status['success']) {
            return [
                'success' => false,
                'message' => "WHM says {$domain} already exists, but no matching cPanel account was found and the stale DNS zone could not be removed: " . ($status['message'] ?: 'Unknown WHM error'),
            ];
        }

        return [
            'success' => true,
            'username' => null,
        ];
    }

    private function waitForDomainRelease($server, string $domain): array
    {
        for ($attempt = 1; $attempt <= 5; $attempt++) {
            sleep($attempt === 1 ? 1 : 2);

            if (!$this->findAccountUsernameByDomain($server, $domain)) {
                return ['success' => true];
            }
        }

        return [
            'success' => false,
            'message' => "The old cPanel account was removed, but WHM still reports {$domain} in userdata. Run /scripts/updateuserdomains on the WHM server or remove the stale userdata entry, then approve again.",
        ];
    }

    private function findAccountUsernameByDomain($server, string $domain): ?string
    {
        $domain = $this->normalizeDomain($domain);

        $userdataUser = $this->findAccountUsernameFromDomainUserdata($server, $domain);

        if ($userdataUser) {
            return $userdataUser;
        }

        $response = $this->whmRequest($server, 'listaccts', [
            'api.version' => 1,
            'searchtype' => 'domain',
            'search' => $domain,
        ]);

        $response = json_decode($response);
        $status = $this->whmResponseStatus($response);

        if (!$status['success']) {
            return null;
        }

        foreach (@$response->data->acct ?? [] as $account) {
            if ($this->normalizeDomain((string) @$account->domain) === $domain) {
                return (string) @$account->user ?: null;
            }
        }

        return null;
    }

    private function findAccountUsernameFromDomainUserdata($server, string $domain): ?string
    {
        $response = $this->whmRequest($server, 'domainuserdata', [
            'api.version' => 1,
            'domain' => $domain,
        ]);

        $response = json_decode($response);
        $status = $this->whmResponseStatus($response);

        if (!$status['success']) {
            return null;
        }

        $userdata = @$response->data->userdata;
        $candidates = [
            @$userdata->user,
            @$userdata->username,
            @$userdata->owner,
            @$response->data->user,
            @$response->data->username,
            @$response->data->owner,
        ];

        foreach ($candidates as $candidate) {
            $candidate = trim((string) $candidate);

            if ($candidate !== '') {
                return $candidate;
            }
        }

        return null;
    }

    private function isExistingDomainError(string $message): bool
    {
        $message = strtolower($message);

        return str_contains($message, 'domain already exists')
            || (str_contains($message, 'domain') && str_contains($message, 'already exists'))
            || (str_contains($message, 'already exists') && str_contains($message, 'userdata'))
            || str_contains($message, 'domain exists')
            || str_contains($message, 'already exists in userdata')
            || str_contains($message, 'already exists in the userdata')
            || str_contains($message, 'already configured')
            || str_contains($message, 'dns entry') && str_contains($message, 'already exists');
    }

    private function normalizeDomain(string $domain): string
    {
        $domain = strtolower(trim($domain));
        $domain = preg_replace('#^https?://#', '', $domain);
        $domain = preg_replace('#/.*$#', '', $domain);

        return ltrim($domain, '.');
    }

    private function curlOptions(): array
    {
        if (defined('CURLOPT_IPRESOLVE') && defined('CURL_IPRESOLVE_V4')) {
            return [
                'curl' => [
                    CURLOPT_IPRESOLVE => CURL_IPRESOLVE_V4,
                ],
            ];
        }

        return [];
    }

    private function failedWhmResponse(string $message): string
    {
        return json_encode([
            'metadata' => [
                'result' => 0,
                'reason' => $message,
            ],
        ]);
    }

    private function cleanCurlMessage(string $message): string
    {
        if (str_contains($message, 'cURL error 28')) {
            return 'The request timed out. Confirm the WHM host is reachable from this server, port 2087 is open, DNS resolves correctly, and the firewall allows outbound HTTPS.';
        }

        return $message;
    }

    private function matchingPackageName(array $packages, string $basePackageName): ?string
    {
        $basePackageName = strtolower($this->normalizePackageName($basePackageName));

        foreach ($packages as $package) {
            $normalized = strtolower($this->normalizePackageName($package));

            if ($normalized === $basePackageName || str_ends_with($normalized, '_' . $basePackageName)) {
                return $package;
            }
        }

        return null;
    }

    private function cpanelSafePackageName($product): string
    {
        $name = $product->package_name ?: $product->slug ?: $product->name;
        $name = $this->normalizePackageName($name);

        if (!$name) {
            $name = 'product_' . $product->id;
        }

        return substr($name, 0, 48);
    }

    private function normalizePackageName(string $name): string
    {
        $name = strtolower(trim($name));
        $name = preg_replace('/[^a-z0-9_]+/', '_', $name);
        $name = preg_replace('/_+/', '_', $name);

        return trim($name, '_');
    }

    private function cleanWhmReason(string $reason): string
    {
        if (str_contains($reason, '. at')) {
            return explode('. at', $reason)[0];
        }

        return $reason;
    }

    private function packageLimitsFromProduct($product): array
    {
        return $this->packageLimitsFromText((string) $product->description);
    }

    private function accountLimitsFromHosting($hosting): array
    {
        $text = (string) @$hosting->product->description;

        $hosting->loadMissing('hostingConfigs.select', 'hostingConfigs.option');

        foreach ($hosting->hostingConfigs as $config) {
            $text .= "\n" . @$config->select->name . ' ' . @$config->option->name;
        }

        $limits = $this->packageLimitsFromText($text);

        return [
            'QUOTA' => $limits['quota'],
            'BWLIMIT' => $limits['bwlimit'],
            'MAXFTP' => $limits['maxftp'],
            'MAXSQL' => $limits['maxsql'],
            'MAXPOP' => $limits['maxpop'],
            'MAXLST' => $limits['maxlst'],
            'MAXSUB' => $limits['maxsub'],
            'MAXPARK' => $limits['maxpark'],
            'MAXADDON' => $limits['maxaddon'],
        ];
    }

    private function packageLimitsFromText(string $text): array
    {
        $description = strtolower($text);
        $websiteLimit = $this->websiteLimit($description);

        return [
            'quota' => $this->storageLimitMb($description),
            'bwlimit' => $this->bandwidthLimitMb($description),
            'maxftp' => $this->ftpLimit($description),
            'maxsql' => $this->databaseLimit($description),
            'maxpop' => $this->emailLimit($description),
            'maxlst' => 0,
            'maxsub' => $this->subdomainLimit($description),
            'maxpark' => 5,
            'maxaddon' => $websiteLimit > 0 ? max(0, $websiteLimit - 1) : 999,
        ];
    }

    private function storageLimitMb(string $description): int
    {
        if (preg_match('/(\d+(?:\.\d+)?)\s*(tb|gb|mb)\s*(?:ssd\s+|nvme\s+)?(?:storage|disk|disk\s+space|web\s+space)/', $description, $match)) {
            return max(1, $this->toMb((float) $match[1], $match[2]));
        }

        if (preg_match('/unlimited\s+(?:ssd\s+|nvme\s+)?(?:storage|disk|disk\s+space|web\s+space)/', $description)) {
            return 1048576;
        }

        return 10240;
    }

    private function bandwidthLimitMb(string $description): int
    {
        if (preg_match('/(\d+(?:\.\d+)?)\s*(tb|gb|mb)\s*(?:bandwidth|transfer|traffic)/', $description, $match)) {
            return max(1, $this->toMb((float) $match[1], $match[2]));
        }

        if (str_contains($description, 'unlimited bandwidth') || str_contains($description, 'unlimited transfer') || str_contains($description, 'unlimited traffic')) {
            return 10485760;
        }

        return 102400;
    }

    private function websiteLimit(string $description): int
    {
        if (preg_match('/(\d+)\s+websites?/', $description, $match)) {
            return (int) $match[1];
        }

        return str_contains($description, 'unlimited websites') ? 0 : 1;
    }

    private function emailLimit(string $description): int
    {
        if (str_contains($description, 'unlimited email')) {
            return 999;
        }

        if (preg_match('/(\d+)\s+email accounts?/', $description, $match)) {
            return (int) $match[1];
        }

        return 5;
    }

    private function databaseLimit(string $description): int
    {
        if (str_contains($description, 'unlimited database')) {
            return 999;
        }

        if (preg_match('/(\d+)\s+(?:mysql\s+|postgresql\s+)?databases?/', $description, $match)) {
            return (int) $match[1];
        }

        return 5;
    }

    private function ftpLimit(string $description): int
    {
        if (str_contains($description, 'unlimited ftp')) {
            return 999;
        }

        if (preg_match('/(\d+)\s+ftp(?:\s+accounts?)?/', $description, $match)) {
            return (int) $match[1];
        }

        return 5;
    }

    private function subdomainLimit(string $description): int
    {
        if (str_contains($description, 'unlimited subdomain')) {
            return 999;
        }

        if (preg_match('/(\d+)\s+subdomains?/', $description, $match)) {
            return (int) $match[1];
        }

        return 20;
    }

    private function toMb(float $value, string $unit): int
    {
        return match (strtolower($unit)) {
            'tb' => (int) round($value * 1024 * 1024),
            'gb' => (int) round($value * 1024),
            default => (int) round($value),
        };
    }

    protected function getProcessedAccountSummary($accountSummary){

        $summary = [];
        $selectedKey = [
            "outgoing_mail_suspended",
            "backup",
            "user",
            "plan",
            "maxpop",
            "legacy_backup",
            "max_defer_fail_percentage",
            "maxftp",
            "max_emailacct_quota",
            "uid",
            "maxsql",
            "theme",
            "suspendreason",
            "diskused",
            "domain",
            "ip",
            "maxparked",
            "maxaddons",
            "temporary",
            "min_defer_fail_to_trigger_protection",
            "is_locked",
            "startdate",
            "unix_startdate",
            "maxlst",
            "partition",
            "email",
            "outgoing_mail_hold",
            "disklimit",
            "maxsub",
            "suspended",
            "inodeslimit",
            "shell",
            "mailbox_format",
            "inodesused",
            "max_email_per_hour",
            "owner",
            "suspendtime"
        ];

        foreach($selectedKey as $key){
            if(isset($accountSummary->$key)){
                $summary[$key] = $accountSummary->$key;
            }else{
                $summary[$key] = null;
            }
        }

        $used = (int) @$accountSummary->diskused;
        $limit = (int) @$accountSummary->disklimit;

        if ($limit == 'unlimited' || $used == 0) {
            $used = 0;
            $limit = 1;
        }

        $diskUsagePercent = ($used / $limit) * 100;
        $summary['disk_usage_percent'] = $accountSummary ? round($diskUsagePercent, 2) . '%' : null;

        return $summary;
    }

    protected function whmResponseStatus($response){

        $success = true;
        $message = null;

        if (!$response || !is_object($response)) {
            return [
                'success' => false,
                'message' => 'Invalid or empty response from cPanel/WHM. Check the server hostname, port, SSL, and authentication credentials.',
            ];
        }

        if (!isset($response->metadata)) {
            $reason = @$response->cpanelresult->data->reason
                ?? @$response->cpanelresult->error
                ?? @$response->error
                ?? 'cPanel/WHM did not return metadata for this request';

            return [
                'success' => false,
                'message' => is_array($reason) ? implode('. ', $reason) : $reason,
            ];
        }

        if (!isset($response->metadata->result)) {
            return [
                'success' => false,
                'message' => @$response->metadata->reason ?: 'cPanel/WHM returned an incomplete response',
            ];
        }

        if(@$response->metadata->result == 0){

            $success = false;

            $reason = (string) (@$response->metadata->reason ?: 'cPanel/WHM request failed');

            if(str_contains($reason, '. at') !== false){
                $message = explode('. at', $reason)[0];
            }else{
                $message = $reason;
            }
        }

        return [
            'success'=>$success, 
            'message'=>$message
        ];
    }

    protected function adminNotification($data, $message, $url = null){
        $adminNotification = new AdminNotification();
        $adminNotification->user_id = @$data->user_id ?? 0;
        $adminNotification->title = gettype($message) == 'array' ? implode('. ', $message) : $message;
        $adminNotification->api_response = 1;
        $adminNotification->click_url = $url ? $url : urlPath('admin.order.hosting.details', $data->id);
        $adminNotification->save();
    }
}

 
