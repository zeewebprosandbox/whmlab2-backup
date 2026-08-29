<?php

namespace App\Http\Controllers;

use App\Lib\CurlRequest;
use App\Models\Hosting;
use App\Models\Domain;
use App\Models\Invoice;
use App\Models\ShoppingCart;
use App\Models\BillingSetting;
use App\Models\InvoiceItem;
use App\Lib\SendServiceEmail;
use App\Models\CronJob;
use App\Models\CronJobLog;
use Carbon\Carbon;
use Status;

class CronController extends Controller{
    
    protected $limit; 
    protected $billingSetting;
    protected $selectInvoiceColumns;

    public function __construct(){
        $this->limit = 100;
        $this->billingSetting = BillingSetting::first();
        $this->selectInvoiceColumns = 'id, reminder, user_id, amount, status, due_date, created, last_cron, hosting_id, domain_id'; 
    }
 
    public function all(){

        $general            = gs();
        $general->last_cron = now();
        $general->save();

        $this->invoiceGenerate(); 
        $this->checkServiceExpiriesAndReminders();
        $this->unpaidInvoiceReminder();
        $this->firstOverdueReminder();
        $this->secondOverdueReminder();
        $this->thirdOverdueReminder();
        $this->autoSuspendOverdueServices();
        $this->addLateFee();
        $this->removeShoppingCarts();
        $this->syncDefaultDnsZones();
        $this->syncAllServerAccounts();
        $this->autoProvisionPendingOrders();

        $notify[] = ['success', 'Automated billing, renewal reminders, server sync, DNS zones, and AutoSSL cron ran successfully'];
        return back()->withNotify($notify);
    }

    public function cron()
    {
        $general            = gs();
        $general->last_cron = now();
        $general->save();

        // 1. Run all master automation workflows directly
        $this->invoiceGenerate(); 
        $this->checkServiceExpiriesAndReminders();
        $this->unpaidInvoiceReminder();
        $this->firstOverdueReminder();
        $this->secondOverdueReminder();
        $this->thirdOverdueReminder();
        $this->autoSuspendOverdueServices();
        $this->addLateFee();
        $this->removeShoppingCarts();
        $this->syncDefaultDnsZones();
        $this->syncAllServerAccounts();
        $this->autoProvisionPendingOrders();

        // 2. Run any custom scheduled database cron jobs
        $crons = CronJob::with('schedule');
        if (request()->alias) {
            $crons->where('alias', request()->alias);
        } else {
            $crons->where('next_run', '<', now())->where('is_running', Status::YES);
        }
        $crons = $crons->get();
        foreach ($crons as $cron) {
            $cronLog              = new CronJobLog();
            $cronLog->cron_job_id = $cron->id;
            $cronLog->start_at    = now();
            if ($cron->is_default) {
                $controller = new $cron->action[0];
                try {
                    $method = $cron->action[1];
                    $controller->$method();
                } catch (\Exception $e) {
                    $cronLog->error = $e->getMessage();
                }
            } else {
                try {
                    CurlRequest::curlContent($cron->url);
                } catch (\Exception $e) {
                    $cronLog->error = $e->getMessage();
                }
            }
            $cron->last_run = now();
            if ($cron->schedule) {
                $cron->next_run = now()->addSeconds($cron->schedule->interval);
            }
            $cron->save();

            $cronLog->end_at = $cron->last_run;
            $startTime         = Carbon::parse($cronLog->start_at);
            $endTime           = Carbon::parse($cronLog->end_at);
            $diffInSeconds     = $startTime->diffInSeconds($endTime);
            $cronLog->duration = $diffInSeconds;
            $cronLog->save();
        }

        if (request()->target == 'all') {
            $notify[] = ['success', 'Cron executed successfully'];
            return back()->withNotify($notify);
        }
        if (request()->alias) {
            $notify[] = ['success', keyToTitle(request()->alias) . ' executed successfully'];
            return back()->withNotify($notify);
        }

        return response("OK - ZodHost automated cron executed successfully at " . now(), 200)->header('Content-Type', 'text/plain');
    }

    public function syncAllServerAccounts()
    {
        try {
            return \App\Http\Controllers\Admin\ServerController::syncAllServerAccounts();
        } catch (\Throwable $e) {
            return 0;
        }
    }

    public function autoProvisionPendingOrders()
    {
        $pendingHostings = Hosting::where('status', 0)->with('user', 'product.serverGroup.servers', 'server.group')->get();
        $whmpanel = new \App\HostingModule\Server\Whmpanel();
        $count = 0;

        foreach ($pendingHostings as $h) {
            try {
                if (!$h->server_id) {
                    $product = $h->product;
                    if ($product) {
                        $selectedServer = \App\Models\Server::bestForProduct($product);
                        if ($selectedServer) {
                            $h->server_id = $selectedServer->id;
                            $h->setRelation('server', $selectedServer);
                            $h->save();
                        }
                    }
                }

                $res = $whmpanel->create($h);
                if (!empty($res['success'])) {
                    $h->status = 1;
                    $h->save();
                    $whmpanel->enforceDefaultDnsZone($h);
                    $count++;
                }
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::error("Auto-provision error for hosting #{$h->id}: " . $e->getMessage());
            }
        }

        return $count;
    }

    public function invoiceGenerate(){
        
        $billingSetting = $this->billingSetting;

        $enableForHosting = false;
        $enableForDomain = false;

        if($billingSetting->create_default_invoice_days != 0){
            $enableForHosting = true;
            $enableForDomain = true;
        }
        else{
            $array = (array) $billingSetting->create_invoice;

            if(array_filter($array)){
                $enableForHosting = true;
            }

            if($billingSetting->create_domain_invoice_days != 0){
                $enableForDomain = true;
            }
        }
 
        if($enableForHosting){ 
            //invoice 0 means empty invoice/allow creating a new invoice
            $hostings = Hosting::active()->where('invoice', 0)->where('billing_cycle', '!=', 0)
                                ->where('next_invoice_date', '<=', Carbon::now())->orderBy('last_cron')->limit($this->limit)
                                ->with([
                                    'hostingConfigs'=>function($config){
                                        $config->select('id', 'hosting_id', 'configurable_group_option_id', 'configurable_group_sub_option_id');
                                    },
                                    'hostingConfigs.select'=>function($configName){
                                        $configName->select('id', 'name', 'option_type', 'configurable_group_id');
                                    },
                                    'hostingConfigs.option'=>function($configValue){
                                        $configValue->select('id', 'name', 'configurable_group_id', 'configurable_group_option_id');
                                    },
                                    'product'=>function($product){
                                        $product->select('id', 'category_id', 'name')->with('serviceCategory', function($category){
                                            $category->select('id', 'name');
                                        });
                                    }
                                ])
                               ->get(['id', 'invoice', 'user_id', 'product_id', 'recurring_amount', 'billing_cycle', 'status', 
                                'next_invoice_date', 'next_due_date', 'last_cron','setup_fee']);
        
            $this->generateHostingInvoice($hostings);
        }

        if($enableForDomain){
            //invoice 0 means empty invoice/allow creating a new invoice
            $domains = Domain::active()->where('invoice', 0)->where('next_invoice_date', '<=', Carbon::now())->orderBy('last_cron')->limit($this->limit)
                             ->get(['id', 'invoice', 'user_id', 'domain', 'id_protection', 'recurring_amount', 'next_due_date', 'reg_period', 'last_cron', 'reg_period']);
         
            $this->generateDomainInvoice($domains);
        } 

    }

    protected function generateHostingInvoice($hostings){
        
        foreach($hostings as $hosting){ 
            $billingCycle = billingCycle($hosting->billing_cycle, true);
         
            $invoice = new Invoice();
            $invoice->hosting_id = $hosting->id;
            $invoice->user_id = $hosting->user_id;

            $invoice->reminder = $invoice->updateReminder();
            /**
            * updateReminder is an array for managing the invoice reminding notify process 
            * @see \App\Models\Invoice go to updateReminder method 
            */
            $invoice->amount = $hosting->recurring_amount;
            $invoice->due_date = $hosting->next_due_date;
            $invoice->status = 2; //2 means Unpaid
            /**
            * For knowing about the status 
            * @see \App\Models\Invoice go to status method 
            */
            $invoice->created = now();
            $invoice->next_due_date = @$this->hostingNextDueDate($billingCycle['billing_cycle'], $hosting);
            $invoice->save(); 

            $hosting->invoice = 1; //0 means empty invoice/allow creating a new invoice

            $hosting->last_cron = Carbon::now();
            $hosting->save();

            $this->invoiceItemForHosting($hosting, $invoice);
            $this->tax($invoice);
        } 
    }

    protected function invoiceItemForHosting($hosting, $invoice){
        $product = $hosting->product;
        $domainText = $hosting->domain ? ' - ' .$hosting->domain : null; 

        $date = ' ('.showDateTime($hosting->next_due_date, 'd/m/Y').' - '.showDateTime($invoice->next_due_date, 'd/m/Y') .')';
        $text = $product->name . $domainText. $date ."\n".$product->serviceCategory->name;
  
        foreach($hosting->hostingConfigs as $config){
            $text = $text ."\n". $config->select->name.': '.$config->option->name;
        }

        $item = new InvoiceItem(); 
        $item->invoice_id = $invoice->id;
        $item->user_id = $invoice->user_id;
        $item->hosting_id = $hosting->id;
        $item->description = $text;
        $item->amount = $hosting->recurring_amount;
        $item->trx_type = '+';
        $item->save();
    }

    protected function generateDomainInvoice($domains){
       
        foreach($domains as $domain){
            $invoice = new Invoice();
            $invoice->domain_id = $domain->id;
            $invoice->user_id = $domain->user_id;

            $invoice->reminder = $invoice->updateReminder();
            /**
            * updateReminder is an array for managing the invoice reminding notify process 
            * @see \App\Models\Invoice go to updateReminder method 
            */
            $invoice->amount = $domain->recurring_amount;
            $invoice->due_date = $domain->next_due_date;
            $invoice->status = 2; //2 means Unpaid
            /**
            * For knowing about the status 
            * @see \App\Models\Invoice go to status method 
            */
            $invoice->created = now();
            $invoice->next_due_date = $this->domainNextDueDate($domain);
            $invoice->save(); 

            $domain->invoice = 1; //0 means empty invoice/allow creating a new invoice

            $domain->last_cron = Carbon::now();
            $domain->save();

            $this->invoiceItemForDomain($invoice, $domain);
            $this->tax($invoice);
        }  
    }

    protected function invoiceItemForDomain($invoice, $domain){
        $domainText = ' - '. $domain->domain .' - '. $domain->reg_period . ' Year/s';
        $protection = $domain->id_protection ? '+ ID Protection' : null;
        $text = 'Domain Renewal' . $domainText. ' ('.showDateTime($invoice->created_at, 'd/m/Y').' - '.showDateTime($invoice->next_due_date, 'd/m/Y') .')'."\n".$protection;

        $item = new InvoiceItem();
        $item->invoice_id = $invoice->id;
        $item->user_id = $invoice->user_id;
        $item->domain_id = $domain->id;
        $item->item_type = 1; //1 means Domain
        $item->description = $text;
        $item->amount = $domain->recurring_amount;
        $item->reg_period = $domain->reg_period; 
        $item->trx_type = '+';
        $item->next_due_date = $invoice->next_due_date; 
        $item->save();
    }

    public function unpaidInvoiceReminder(){
        
        $billingSetting = $this->billingSetting;

        if($billingSetting->invoice_send_reminder != 1 || $billingSetting->invoice_send_reminder_days == 0){ 
            return false;
        }

        $days = $billingSetting->invoice_send_reminder_days;
        $invoices = $this->invoices('unpaid_reminder', $days, '-');
    
        foreach($invoices as $invoice){
            SendServiceEmail::invoicePaymentReminder($invoice);
        
            $invoice->reminder = $invoice->updateReminder('unpaid_reminder');
            /**
            * updateReminder is an array for managing the invoice reminding notify process 
            * @see \App\Models\Invoice go to updateReminder method 
            */
            $invoice->last_cron = Carbon::now();
            $invoice->save();
        }   

    }
    
    public function firstOverdueReminder(){
      
        $billingSetting = $this->billingSetting;

        if($billingSetting->invoice_send_reminder != 1 || $billingSetting->invoice_first_over_due_reminder == 0){
            return false;
        }

        $days = $billingSetting->invoice_first_over_due_reminder;
        $invoices = $this->invoices('first_over_due_reminder', $days, '+');
        
        foreach($invoices as $invoice){
            SendServiceEmail::firstInvoiceOverdue($invoice);

            $invoice->reminder = $invoice->updateReminder('first_over_due_reminder');
            /**
            * updateReminder is an array for managing the invoice reminding notify process 
            * @see \App\Models\Invoice go to updateReminder method 
            */
            $invoice->last_cron = Carbon::now();
            $invoice->save();
        } 

    }

    public function secondOverdueReminder(){           
        
        $billingSetting = $this->billingSetting;

        if($billingSetting->invoice_send_reminder != 1 || $billingSetting->invoice_second_over_due_reminder == 0){
            return false;
        }

        $days = $billingSetting->invoice_second_over_due_reminder;
        $invoices = $this->invoices('second_over_due_reminder', $days, '+');
        
        foreach($invoices as $invoice){
            SendServiceEmail::secondInvoiceOverdue($invoice);

            $invoice->reminder = $invoice->updateReminder('second_over_due_reminder');
            /**
            * updateReminder is an array for managing the invoice reminding notify process 
            * @see \App\Models\Invoice go to updateReminder method 
            */
            $invoice->last_cron = Carbon::now();
            $invoice->save();
        } 

    }

    public function thirdOverdueReminder(){             
        
        $billingSetting = $this->billingSetting;

        if($billingSetting->invoice_send_reminder != 1 || $billingSetting->invoice_third_over_due_reminder == 0){
            return false;
        }

        $days = $billingSetting->invoice_third_over_due_reminder;
        $invoices = $this->invoices('third_over_due_reminder', $days, '+');

        foreach($invoices as $invoice){
            SendServiceEmail::thirdInvoiceOverdue($invoice);

            $invoice->reminder = $invoice->updateReminder('third_over_due_reminder');
            /**
            * updateReminder is an array for managing the invoice reminding notify process 
            * @see \App\Models\Invoice go to updateReminder method 
            */
            $invoice->last_cron = Carbon::now();
            $invoice->save();
        } 

    }

    public function addLateFee(){       
        
        $billingSetting = $this->billingSetting;

        if($billingSetting->late_fee_days == 0){ //If disable late fee from admin panel
            return false;
        }

        $days = $billingSetting->late_fee_days;
        $invoices = $this->invoices('add_late_fee', $days, '+');
       
        foreach($invoices as $invoice){ 
            $item = new InvoiceItem();
            $item->invoice_id = $invoice->id;
            $item->user_id = $invoice->user_id;
            $item->description = 'Late Fee';
            $item->amount = $billingSetting->getLateFee($invoice->amount);
            $item->save();

            $invoice->reminder = $invoice->updateReminder('add_late_fee');
            /**
            * updateReminder is an array for managing the invoice reminding notify process 
            * @see \App\Models\Invoice go to updateReminder method 
            */
            $invoice->amount = $billingSetting->getLateFee($invoice->amount, true); 
            /**
            * getLateFee is a method for getting the amount of the late fee
            * @see \App\Models\BillingSetting go to getLateFee method 
            */
            $invoice->last_cron = Carbon::now();
            $invoice->save();
        }  
    }

    protected function invoices($column, $days, $addOrLess){
      
        $append = 'Append_'.str_replace(' ', '_', ucwords(str_replace('_', ' ', $column))).'_Date';
        $select = $this->selectInvoiceColumns;
      
        $invoices = Invoice::unpaid()
                    ->whereJsonContains("reminder->$column", 0)
                    ->selectRaw("$select, DATE_FORMAT(due_date $addOrLess interval $days day, '%Y-%m-%d') AS $append")
                    ->whereRaw("DATE_FORMAT(due_date $addOrLess interval $days day, '%Y-%m-%d') = DATE_FORMAT(now(), '%Y-%m-%d')")
                    ->orderBy('last_cron')
                    ->limit($this->limit)
                    ->with('user', function($user){
                        $user->select('id', 'firstname', 'lastname', 'username', 'email', 'mobile', 'country_code');
                    })
                    ->get();
      
        return $invoices;
    }

    protected function hostingNextDueDate($billingCycle, $hosting){
        try{
            $optionA = ['monthly'=>1, 'quarterly'=>3, 'semi_annually'=>6];
            $optionB = ['annually'=>1, 'biennially'=>2, 'triennially'=>3];
            
            $keysForA = array_keys($optionA);
            $keysForB = array_keys($optionB);
            
            if(in_array($billingCycle, $keysForA)){
                return Carbon::parse($hosting->next_due_date)->addMonth($optionA[$billingCycle]);
            }
    
            if(in_array($billingCycle, $keysForB)){
                return Carbon::parse($hosting->next_due_date)->addYear($optionB[$billingCycle]);
            }
        }catch(\Exception $error){
            return $error->getMessage();
        }
        
    }

    protected function domainNextDueDate($domain){
        return Carbon::parse($domain->next_due_date)->addYear($domain->reg_period);
    }

    private function tax($invoice){

        $general = gs();
        $tax = $general->tax;
        
        //Insert new row for tax
        if($tax > 0){
            $item = new InvoiceItem();
            $item->invoice_id = $invoice->id;
            $item->user_id = $invoice->user_id;
            $item->description = "Tax $tax%\n(Before tax ".showAmount($invoice->amount)." $general->cur_text x $tax%)";
            $item->tax = $tax;
            $item->amount = ($invoice->amount * $tax / 100);
            $item->trx_type = '+';
            $item->save();

            $invoice->amount += $item->amount;
            $invoice->save();
        } 
    }

    public function removeShoppingCarts(){
        ShoppingCart::whereDoesntHave('user')->where('created_at', '<', Carbon::now()->subHours(3))->delete();
    }

    public function syncDefaultDnsZones(){
        $services = Hosting::active()->with('server.group')->get();
        $count = 0;
        $whmpanel = new \App\HostingModule\Server\Whmpanel();

        foreach ($services as $service) {
            if ($service->domain && $service->server) {
                try {
                    $whmpanel->enforceDefaultDnsZone($service);
                    $count++;
                } catch (\Throwable $e) {}
            }
        }

        return $count;
    }

    /**
     * Check package expiry dates and send structured notifications 7, 3, 1, and 0 days before due date
     */
    public function checkServiceExpiriesAndReminders()
    {
        $hostings = Hosting::active()->where('billing_cycle', '!=', 0)->with('user', 'product')->get();

        foreach ($hostings as $hosting) {
            if (!$hosting->next_due_date || !$hosting->user) {
                continue;
            }

            $dueDate = Carbon::parse($hosting->next_due_date)->startOfDay();
            $today = Carbon::now()->startOfDay();
            $daysLeft = (int) $today->diffInDays($dueDate, false);

            if (in_array($daysLeft, [7, 3, 1, 0])) {
                $remindKey = "expiry_remind_{$daysLeft}d_" . $dueDate->format('Ymd');
                $notes = (string) $hosting->admin_notes;

                if (!str_contains($notes, $remindKey)) {
                    SendServiceEmail::serviceExpiryReminder($hosting, $daysLeft);
                    \App\Services\TelegramService::notifyServiceExpiryReminder($hosting, $daysLeft);

                    $hosting->admin_notes = trim($notes . "\n[" . now()->format('Y-m-d H:i') . "] " . $remindKey);
                    $hosting->save();
                }
            }
        }
    }

    /**
     * Automatically suspend overdue services on ZodPanel/Hestia and notify customer & channel
     */
    public function autoSuspendOverdueServices()
    {
        $overdueHostings = Hosting::active()
            ->where('billing_cycle', '!=', 0)
            ->where('next_due_date', '<', Carbon::now()->subDays(1)->startOfDay())
            ->with('server.group', 'user', 'product')
            ->get();

        foreach ($overdueHostings as $hosting) {
            $hasUnpaidInvoice = Invoice::where('hosting_id', $hosting->id)->where('status', 2)->exists();
            if (!$hasUnpaidInvoice) {
                continue;
            }

            // 1. Suspend on remote panel
            $serverGroup = @$hosting->server->group;
            if ($serverGroup) {
                try {
                    $req = new \Illuminate\Http\Request([
                        'suspend_reason' => 'Payment overdue past grace period',
                    ]);
                    \App\HostingModule\HostingManager::init($serverGroup)->suspend([
                        'hosting' => $hosting,
                        'request' => $req,
                    ]);
                } catch (\Throwable $e) {
                    \Illuminate\Support\Facades\Log::error("Automated cron suspension failed for hosting #{$hosting->id}: " . $e->getMessage());
                }
            }

            // 2. Update service status
            $hosting->status = 3; // 3 means Suspended
            $hosting->suspend_reason = 'Payment overdue past grace period (Automated Cron)';
            $hosting->suspend_date = now();
            $hosting->save();

            // 3. Send Apple Mail suspension email
            SendServiceEmail::serviceSuspend($hosting, (object) [
                'suspend_reason' => 'Payment overdue past grace period. Please settle your invoice to instantly restore your service.',
            ]);

            // 4. Dispatch Telegram Alert
            \App\Services\TelegramService::notifyServiceSuspended($hosting, 'Payment overdue past grace period (Automated Cron)');
        }
    }

}