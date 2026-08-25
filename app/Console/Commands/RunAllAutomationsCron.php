<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Controllers\CronController;

class RunAllAutomationsCron extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'zodpanel:cron-run {--silent : Run without console output}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Execute all ZodPanel / WHMLab billing, renewal reminders, expiry notices, suspensions, and DNS automations in one unified run.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $start = microtime(true);
        $this->info("⚡ [ZodPanel Automation] Starting complete cron pipeline...");

        try {
            $cron = new CronController();

            $general = gs();
            $general->last_cron = now();
            $general->save();

            $this->line("1. Generating Invoices for upcoming billing cycles...");
            $cron->invoiceGenerate();
            $this->info("   ✓ Invoices generated.");

            $this->line("2. Checking service expiries (7d, 3d, 1d, 0d) and sending multi-stage renewal alerts...");
            $cron->checkServiceExpiriesAndReminders();
            $this->info("   ✓ Expiry & renewal reminders dispatched.");

            $this->line("3. Checking unpaid invoices & sending payment reminders...");
            $cron->unpaidInvoiceReminder();
            $this->info("   ✓ Unpaid reminders sent.");

            $this->line("4. Processing 1st, 2nd, and 3rd overdue invoice notifications...");
            $cron->firstOverdueReminder();
            $cron->secondOverdueReminder();
            $cron->thirdOverdueReminder();
            $this->info("   ✓ Overdue notices processed.");

            $this->line("5. Processing automated service suspensions on ZodPanel/Hestia for past-due accounts...");
            $cron->autoSuspendOverdueServices();
            $this->info("   ✓ Auto-suspensions completed.");

            $this->line("6. Calculating late fees on overdue invoices...");
            $cron->addLateFee();
            $this->info("   ✓ Late fees updated.");

            $this->line("7. Purging stale abandoned shopping carts...");
            $cron->removeShoppingCarts();
            $this->info("   ✓ Abandoned carts purged.");

            $this->line("8. Enforcing and synchronizing default DNS zones...");
            $dnsCount = $cron->syncDefaultDnsZones();
            $this->info("   ✓ {$dnsCount} DNS zone(s) validated.");

            $this->line("9. Synchronizing active server account counters...");
            $activeCount = \App\Models\Hosting::where('status', 1)->count();
            \App\Models\Server::query()->update(['current_accounts' => $activeCount]);
            $this->info("   ✓ Server accounts synchronized ({$activeCount} active).");

            $duration = round(microtime(true) - $start, 3);
            $this->info("🎉 [ZodPanel Automation] All cron jobs completed successfully in {$duration}s!");
            return Command::SUCCESS;
        } catch (\Throwable $e) {
            $this->error("❌ Cron pipeline error: " . $e->getMessage());
            \Illuminate\Support\Facades\Log::error("Cron pipeline exception: " . $e->getMessage());
            return Command::FAILURE;
        }
    }
}
