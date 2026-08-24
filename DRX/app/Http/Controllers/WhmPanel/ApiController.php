<?php

namespace App\Http\Controllers\WhmPanel;

use App\Http\Controllers\Controller;
use App\Models\WhmPanelAccount;
use App\Models\WhmPanelDnsRecord;
use App\Models\WhmPanelNode;
use App\Models\WhmPanelSsoToken;
use App\Models\WhmPanelWebsite;
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

        WhmPanelDnsRecord::firstOrCreate(
            ['website_id' => $website->id, 'name' => '@', 'type' => 'A'],
            ['value' => $account->node->ip_address ?: '127.0.0.1', 'ttl' => 3600]
        );

        return $website;
    }

    private function ok($data, int $status = 200)
    {
        return response()->json([
            'success' => true,
            'data' => $data,
        ], $status);
    }
}
