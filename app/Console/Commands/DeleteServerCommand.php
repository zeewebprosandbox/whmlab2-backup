<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Server;
use App\Models\Hosting;
use App\Models\WhmPanelNode;
use Illuminate\Support\Facades\Schema;

class DeleteServerCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'zodpanel:delete-server {server : The ID, Name, or IP of the server to delete entirely} {--force : Force delete without confirmation}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete any server entirely 100% and safely unlink or prepare attached services for merging';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $serverArg = $this->argument('server');

        $server = Server::where('id', $serverArg)
            ->orWhere('name', $serverArg)
            ->orWhere('ip_address', $serverArg)
            ->orWhere('hostname', 'like', "%{$serverArg}%")
            ->first();

        if (!$server) {
            $this->error("Server not found matching '{$serverArg}'");
            return self::FAILURE;
        }

        $servicesCount = Hosting::where('server_id', $server->id)->count();

        if (!$this->option('force')) {
            if (!$this->confirm("Are you sure you want to delete Server '{$server->name}' (ID: {$server->id}, IP: {$server->ip_address}) entirely 100%? ({$servicesCount} services attached)")) {
                $this->info("Operation cancelled.");
                return self::SUCCESS;
            }
        }

        $this->warn("Deleting Server '{$server->name}' entirely...");

        // Unlink attached services
        Hosting::where('server_id', $server->id)->update([
            'server_id' => 0,
        ]);

        // Delete associated WhmPanelNode records
        if (Schema::hasTable('whm_panel_nodes')) {
            WhmPanelNode::where('server_id', $server->id)->delete();
        }

        $serverName = $server->name;
        $serverIp = $server->ip_address;
        $serverId = $server->id;
        $server->delete();

        $this->info("✓ Server '{$serverName}' (ID: {$serverId}, IP: {$serverIp}) has been deleted entirely 100% successfully!");
        if ($servicesCount > 0) {
            $this->line("<fg=yellow>Notice: {$servicesCount} service(s) were unlinked from this server and are ready to be merged to a new server using:</> <fg=cyan>php artisan zodpanel:merge-service</>");
        }

        return self::SUCCESS;
    }
}
