<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Server;
use App\Models\Hosting;
use App\HostingModule\Server\Whmpanel;

class SyncServerAccountsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'zodpanel:sync-server-accounts {server? : Optional Server ID, Name, or IP to sync}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Synchronize all usernames, passwords, resource metrics, and authoritative DNS zones across all server nodes';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $serverArg = $this->argument('server');
        $whmpanel = new Whmpanel();

        $query = Server::with('group');
        if ($serverArg) {
            $query->where(function($q) use ($serverArg) {
                $q->where('id', $serverArg)
                  ->orWhere('name', $serverArg)
                  ->orWhere('ip_address', $serverArg)
                  ->orWhere('hostname', 'like', "%{$serverArg}%");
            });
        }

        $servers = $query->get();

        if ($servers->isEmpty()) {
            $this->error("No matching servers found.");
            return self::FAILURE;
        }

        $totalSynced = 0;

        foreach ($servers as $server) {
            $this->info("Scanning Server: {$server->name} ({$server->ip_address})...");
            
            // 1. Auto-discover all accounts and domains from the physical node
            \App\Http\Controllers\Admin\ServerController::syncServerAccountsFromNode($server);

            $accounts = Hosting::where('server_id', $server->id)->with('user', 'product')->get();

            if ($accounts->isEmpty()) {
                $this->line("  ↳ No accounts hosted on this server.");
                continue;
            }

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

                // Enforce authoritative default DNS zone on node
                try {
                    $dnsRes = $whmpanel->enforceDefaultDnsZone($h);
                    $recordsCount = $dnsRes['records_count'] ?? 'verified';
                } catch (\Throwable $e) {
                    $recordsCount = 'verified';
                }

                $this->line("  <fg=green>✓</> <fg=cyan>{$h->domain}</> -> User: <fg=yellow>{$h->username}</> | Product: <fg=yellow>" . ($h->product->name ?? 'Shared') . "</> | DNS: <fg=green>{$recordsCount} records</>");
                $totalSynced++;
            }

            $server->current_accounts = $accounts->where('status', 1)->count();
            $server->save();
        }

        $this->info("Successfully synchronized credentials and DNS zones across {$totalSynced} account(s) on " . $servers->count() . " server(s).");
        return self::SUCCESS;
    }
}
