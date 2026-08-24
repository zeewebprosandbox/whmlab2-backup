<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Server;
use App\Support\ZodPanelNodeBootstrapper;

class SyncDesignCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'zodpanel:sync-design {server? : The Server ID, Name, or IP to sync custom Hestia design to}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync custom Hestia/ZodPanel UI design, themes, CSS, JS, and modules to any server node';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $serverArg = $this->argument('server');
        $bootstrapper = app(ZodPanelNodeBootstrapper::class);

        $query = Server::query();
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
            $this->error("No matching server found.");
            return self::FAILURE;
        }

        foreach ($servers as $server) {
            $this->info("Syncing custom Hestia/ZodPanel design to Server '{$server->name}' ({$server->ip_address})...");

            $credentials = [
                'host' => $server->ip_address ?: (parse_url($server->hostname, PHP_URL_HOST) ?: '169.58.176.53'),
                'ssh_port' => (int) ($server->ssh_port ?: 22),
                'ssh_username' => $server->username ?: 'root',
                'ssh_password' => $server->password,
                'panel_hostname' => parse_url($server->hostname, PHP_URL_HOST) ?: 'zodpanel.zodserver.cloud',
                'admin_email' => 'admin@' . (parse_url($server->hostname, PHP_URL_HOST) ?: 'zodpanel.zodserver.cloud'),
            ];

            try {
                $res = $bootstrapper->syncCustomLayer($credentials, $server->api_token, [
                    'onLog' => function($line) {
                        $this->line("  " . $line);
                    }
                ]);
                if ($res['success']) {
                    $server->deployment_status = 'deployed';
                    $server->last_deployed_at = now();
                    $server->save();
                    $this->line("<fg=green>✓ Successfully synced custom design and restarted Hestia services on {$server->name}!</>");
                } else {
                    $this->warn("Sync notice: " . $res['message']);
                }
            } catch (\Throwable $e) {
                $this->error("Sync exception: " . $e->getMessage());
            }
        }

        return self::SUCCESS;
    }
}
