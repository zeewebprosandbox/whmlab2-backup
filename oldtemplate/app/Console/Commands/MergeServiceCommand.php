<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Server;
use App\Models\Hosting;
use App\HostingModule\Server\Whmpanel;

class MergeServiceCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'zodpanel:merge-service {service : The ID or Domain of the hosting service} {server : The Target Server ID, Name, or IP to merge into}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Merge and reassign any hosting service to a target server node with real-time authoritative DNS overwrite';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $serviceArg = $this->argument('service');
        $serverArg = $this->argument('server');

        $hosting = Hosting::where('id', $serviceArg)
            ->orWhere('domain', $serviceArg)
            ->with('server', 'product')
            ->first();

        if (!$hosting) {
            $this->error("Hosting service not found matching '{$serviceArg}'");
            return self::FAILURE;
        }

        $targetServer = Server::where('id', $serverArg)
            ->orWhere('name', $serverArg)
            ->orWhere('ip_address', $serverArg)
            ->orWhere('hostname', 'like', "%{$serverArg}%")
            ->first();

        if (!$targetServer) {
            $this->error("Target server not found matching '{$serverArg}'");
            return self::FAILURE;
        }

        $this->info("Merging Service '{$hosting->domain}' (ID: {$hosting->id}) to Target Server '{$targetServer->name}' (IP: {$targetServer->ip_address})...");

        $hosting->server_id = $targetServer->id;
        $hosting->dedicated_ip = $targetServer->ip_address;
        $hosting->ip = $targetServer->ip_address;
        $hosting->ns1 = $targetServer->ns1 ?: 'ns1.zodserver.cloud';
        $hosting->ns2 = $targetServer->ns2 ?: 'ns2.zodserver.cloud';
        $hosting->ns1_ip = $targetServer->ns1_ip ?: $targetServer->ip_address;
        $hosting->ns2_ip = $targetServer->ns2_ip ?: $targetServer->ip_address;
        $hosting->save();

        // Enforce authoritative DNS zone on the target server node in real-time
        $dnsRes = null;
        try {
            $whmpanel = new Whmpanel();
            $dnsRes = $whmpanel->enforceDefaultDnsZone($hosting);
        } catch (\Throwable $e) {
            $this->warn("DNS sync notice: " . $e->getMessage());
        }

        $this->info("✓ Service '{$hosting->domain}' successfully merged and reassigned to Server '{$targetServer->name}' ({$targetServer->ip_address}) in real time!");
        if ($dnsRes && @$dnsRes['success']) {
            $this->line("<fg=green>✓ Authoritative default DNS zone overwritten on target server ({$dnsRes['records_count']} records active)</>");
        }

        return self::SUCCESS;
    }
}
