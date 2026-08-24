<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Hosting;
use App\HostingModule\Server\Whmpanel;

class EnforceDefaultDns extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'zodpanel:enforce-dns {domain? : Optional specific domain to enforce}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Enforce authoritative default DNS records and overwrite any obstructive records for all active domains using our nameservers';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $domainArg = $this->argument('domain');
        $whmpanel = new Whmpanel();

        if ($domainArg) {
            $this->info("Enforcing default DNS records for: {$domainArg}");
            $hosting = Hosting::active()->where('domain', $domainArg)->with('server.group')->first();
            if (!$hosting) {
                $this->error("No active hosting found for domain '{$domainArg}'");
                return self::FAILURE;
            }

            $res = $whmpanel->enforceDefaultDnsZone($hosting);
            $this->info("[SUCCESS] {$res['message']} ({$res['records_count']} records active)");
            return self::SUCCESS;
        }

        $this->info("Scanning all active hosting domains to enforce default authoritative DNS records...");
        $services = Hosting::active()->with('server.group')->get();
        $total = 0;

        foreach ($services as $service) {
            if ($service->domain && $service->server) {
                $res = $whmpanel->enforceDefaultDnsZone($service);
                $this->line("<fg=green>✓</> <fg=cyan>{$service->domain}</> -> Overwritten to pristine default records on node <fg=yellow>{$service->server->name}</> ({$res['records_count']} records)");
                $total++;
            }
        }

        $this->info("Successfully enforced and verified default DNS records across {$total} domain(s) in real time.");
        return self::SUCCESS;
    }
}
