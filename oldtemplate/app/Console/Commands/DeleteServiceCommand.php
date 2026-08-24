<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Hosting;
use App\Models\Server;
use App\Models\WhmPanelAccount;
use App\Models\WhmPanelWebsite;
use App\Models\WhmPanelDnsRecord;
use App\Models\WhmPanelDatabase;
use App\Models\WhmPanelMailAccount;
use App\Models\InvoiceItem;
use Illuminate\Support\Facades\Schema;

class DeleteServiceCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'zodpanel:delete-service {service? : The ID or Domain of the hosting service to delete} {--all : Delete ALL hosting services in the database} {--force : Force deletion without confirmation prompt}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete any hosting service or all services entirely 100% and clean up linked accounts, websites, and DNS records';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        if ($this->option('all')) {
            $count = Hosting::count();

            if ($count === 0) {
                $this->info("No hosting services found in the database.");
                return self::SUCCESS;
            }

            if (!$this->option('force')) {
                if (!$this->confirm("Are you sure you want to delete ALL {$count} hosting services entirely 100%?")) {
                    $this->info("Operation cancelled.");
                    return self::SUCCESS;
                }
            }

            $this->warn("Deleting ALL {$count} hosting service(s) entirely...");

            if (Schema::hasTable('whm_panel_dns_records')) {
                WhmPanelDnsRecord::truncate();
            }
            if (Schema::hasTable('whm_panel_websites')) {
                WhmPanelWebsite::truncate();
            }
            if (Schema::hasTable('whm_panel_accounts')) {
                WhmPanelAccount::truncate();
            }

            Server::query()->update(['current_accounts' => 0]);
            Hosting::query()->delete();

            $this->info("✓ Successfully deleted ALL {$count} hosting service(s) entirely 100%!");
            return self::SUCCESS;
        }

        $serviceArg = $this->argument('service');
        if (!$serviceArg) {
            $this->error("Please provide a service ID or domain, or pass --all to delete all services.");
            return self::FAILURE;
        }

        $service = Hosting::where('id', $serviceArg)
            ->orWhere('domain', $serviceArg)
            ->with('server', 'user')
            ->first();

        if (!$service) {
            $this->error("Hosting service not found matching '{$serviceArg}'");
            return self::FAILURE;
        }

        $domain = $service->domain ?: '#' . $service->id;

        if (!$this->option('force')) {
            if (!$this->confirm("Are you sure you want to delete Service '{$domain}' (ID: {$service->id}) entirely 100%?")) {
                $this->info("Operation cancelled.");
                return self::SUCCESS;
            }
        }

        $this->warn("Deleting Service '{$domain}'...");

        // Clean up linked mirror accounts & records
        if (Schema::hasTable('whm_panel_accounts')) {
            $account = WhmPanelAccount::where('hosting_id', $service->id)->first();
            if ($account) {
                if (Schema::hasTable('whm_panel_websites')) {
                    $websites = WhmPanelWebsite::where('account_id', $account->id)->get();
                    foreach ($websites as $w) {
                        if (Schema::hasTable('whm_panel_dns_records')) {
                            WhmPanelDnsRecord::where('website_id', $w->id)->delete();
                        }
                        $w->delete();
                    }
                }
                if (Schema::hasTable('whm_panel_databases')) {
                    WhmPanelDatabase::where('account_id', $account->id)->delete();
                }
                if (Schema::hasTable('whm_panel_mail_accounts')) {
                    WhmPanelMailAccount::where('account_id', $account->id)->delete();
                }
                $account->delete();
            }
        }

        if ($service->server && $service->server->current_accounts > 0) {
            $service->server->decrement('current_accounts');
        }

        InvoiceItem::where('hosting_id', $service->id)->delete();
        $service->delete();

        $this->info("✓ Service '{$domain}' has been deleted entirely 100% successfully!");
        return self::SUCCESS;
    }
}
