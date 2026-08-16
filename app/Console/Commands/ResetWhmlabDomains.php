<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\Domain;
use App\Models\Hosting;

class ResetWhmlabDomains extends Command
{
    protected $signature = 'zodpanel:reset-domains {--force : Force the operation without confirmation prompt}';

    protected $description = 'Clear all domain and hosting records in WHMLab to start completely fresh';

    public function handle(): int
    {
        if (!$this->option('force')) {
            if (!$this->confirm('Are you sure you want to delete ALL domain and hosting records in WHMLab? This cannot be undone.')) {
                $this->info('Operation cancelled.');
                return self::SUCCESS;
            }
        }

        $this->warn('Clearing WHMLab domain records...');

        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('domains')->truncate();
        DB::table('hostings')->truncate();
        if (\Illuminate\Support\Facades\Schema::hasTable('whm_panel_accounts')) {
            DB::table('whm_panel_accounts')->truncate();
        }
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $this->line('Domain and hosting database tables truncated.');

        $this->info('WHMLab domain records cleared successfully! You can now start fresh.');
        return self::SUCCESS;
    }
}
