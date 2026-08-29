<?php

namespace App\Http\Controllers\User;

use App\Constants\Status;
use App\Http\Controllers\Controller;
use App\Lib\AfterPayment;
use App\Models\BillingSetting;
use App\Models\Deposit;
use App\Models\Domain;
use App\Models\DomainRegister;
use App\Models\Frontend;
use App\Models\GatewayCurrency;
use App\Models\Hosting;
use App\Models\HostingConfig;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Order;
use App\Models\ShoppingCart;
use Carbon\Carbon;
use Illuminate\Http\Request; 
use PDF; 

class InvoiceController extends Controller{ 

    public function create(Request $request){

        $user = auth()->user();
        $carts = ShoppingCart::whereBelongsTo($user)->get();
        $billingSetting = BillingSetting::first();

        if ($request->isMethod('get')) {
            if (!$carts || !$carts->count()) {
                $notify[] = ['info', 'Your shopping cart is currently empty.'];
                return to_route('services')->withNotify($notify);
            }
            return to_route('shopping.cart');
        }

        if(!$carts || !$carts->count()){
            $notify[] = ['error', 'Your shopping cart is empty. Please add an item before checkout.'];
            return to_route('shopping.cart')->withNotify($notify);
        }

        if($request->filled('payment')){
            $this->validatePaymentRequest($request);
        }

        $totalPrice = (float) $carts->sum('total');
        $totalDiscount = (float) $carts->sum('discount');
        $afterDiscount = (float) $carts->sum('after_discount');

        if ($afterDiscount <= 0 && $totalPrice > 0) {
            $afterDiscount = max(0, $totalPrice - $totalDiscount);
        }

        if($request->payment == 'wallet'){
            $walletPayable = $this->walletPayableAmount($afterDiscount);

            if(!gs('deposit_module')){
                $notify[] = ['warning', 'You cannot use wallet balance right now'];
                return to_route('shopping.cart')->withNotify($notify);
            }

            if($walletPayable > $user->balance){
                $notify[] = ['error', 'Your wallet balance is insufficient for this checkout. Please select a payment gateway or top up your balance.'];
                return to_route('shopping.cart')->withNotify($notify);
            }
        }
        
        $invoice = new Invoice();
        $invoice->user_id = $user->id;
        $invoice->amount = $afterDiscount;
        $invoice->due_date = now();
        $invoice->status = 2; // 2 means Unpaid
        /**
        * For knowing status type 
        * @see \App\Models\Invoice go to status method 
        */
        $invoice->created = now();
        $invoice->reminder = $invoice->updateReminder();
        /**
        * updateReminder is an array for managing the invoice reminding notify process 
        * @see \App\Models\Invoice go to updateReminder method 
        */
        $invoice->save();

        $order = new Order();
        $order->user_id = $user->id;
        $order->invoice_id = $invoice->id;
        $order->coupon_id = @$carts->where('discount_applied',1)->where('coupon_id', '!=', 0)->first()->coupon_id ?? 0;
        $order->amount = $totalPrice;
        $order->discount = $totalDiscount; 
        $order->after_discount = $afterDiscount; 
        $order->ip_address = getRealIP();
        $order->status = 2; // 2 means Pending
        /**
        * For knowing status type 
        * @see \App\Models\Order go to status method 
        */
        $order->save();

        foreach($carts as $cart){

            //For service/hosting
            if($cart->product_id && !$cart->domain_setup_id && !$cart->domain_id){

                $product = $cart->product;

                $billing = @billingCycle(@$cart->billing_cycle, true);
                $nextDueDate = @$billing['carbon'];
                $days = @$billingSetting->create_invoice->{@$billing['billing_cycle']}; 

                if(!$days){
                    $days = $billingSetting->create_default_invoice_days;
                }

                $nextInvoiceDate = Carbon::parse($nextDueDate)->subDays($days)->toDateTimeString();

                $hosting = new Hosting();
                $hosting->order_id = $order->id;
                $hosting->product_id = $cart->product_id;
                $hosting->domain_setup_id = $cart->domain_setup_id; 
                $hosting->domain = $cart->domain ?? $cart->hostname;

                $assignedServer = $product?->server ?: \App\Models\Server::bestForProduct($product) ?: \App\Models\Server::where('status', 1)->first() ?: \App\Models\Server::first();
                $hosting->server_id = $assignedServer?->id ?: $product?->server_id;
                $hosting->first_payment_amount = $cart->after_discount;
                $hosting->recurring_amount = $cart->price;
                $hosting->discount = $cart->discount;
                $hosting->setup_fee = $cart->setup_fee; 
                $hosting->billing_cycle = $cart->billing_cycle; 
                $hosting->next_due_date = $nextDueDate;
                $hosting->next_invoice_date = $nextInvoiceDate;
                $hosting->stock_control = $product->stock_control;
                $hosting->user_id = $user->id;
                $hosting->password = $cart->password ?: \Illuminate\Support\Str::password(16);
                $domainPart = preg_replace('/[^a-z0-9]/i', '', explode('.', $hosting->domain ?: 'srv')[0]);
                $hosting->username = $hosting->username ?: (substr($domainPart, 0, 8) . rand(10, 99));
                $hosting->status = 1; // 1 means Active
                $hosting->reg_date = Carbon::now();
                $hosting->ns1 = $cart->ns1 ?: @$assignedServer?->ns1 ?: 'ns1.zodserver.cloud';
                $hosting->ns2 = $cart->ns2 ?: @$assignedServer?->ns2 ?: 'ns2.zodserver.cloud';
                $hosting->save();
                
                $this->hostingConfigs($hosting->id, $cart->config_options);
                $this->invoiceItemForHosting($invoice, $hosting, $product);

                try {
                    $whmpanel = new \App\HostingModule\Server\Whmpanel();
                    $whmpanel->create($hosting);
                } catch (\Throwable $e) {
                    \Illuminate\Support\Facades\Log::warning("Immediate hosting provisioning on checkout warning: " . $e->getMessage());
                }
            }
            else{
                /**
                * For knowing about the type 
                * @see \App\Models\ShoppingCart go to type method 
                */
                if($cart->type == 4){
                    $domain = $cart->getDomain;
                }
                else{  
                    $domain = new Domain(); 
                    $domain->order_id = $order->id;

                    //Finding hosting for the domain
                    $hosting = @Hosting::whereBelongsTo($user)->where('product_id', $cart->product_id)->where('domain', $cart->domain)->where('billing_cycle', $cart->billing_cycle)->orderBy('id', 'DESC')->first('id');
                   
                    $domain->hosting_id = @$hosting->id ?? 0;

                    $domain->domain_register_id = @DomainRegister::default()->first('id')->id ?? 0;
                    $domain->user_id = $user->id;
                    $domain->domain_setup_id = $cart->domain_setup_id;
                    $domain->coupon_id = $cart->coupon_id; 
                    $domain->domain = $cart->domain;
                    $domain->id_protection = $cart->id_protection; 
                    $domain->first_payment_amount = $cart->after_discount;
                    $domain->recurring_amount = $cart->total;
                    $domain->discount = $cart->discount;
                    $domain->reg_period = $cart->reg_period;
                    $domain->next_due_date = Carbon::now()->addYears($cart->reg_period)->toDateTimeString();
                    $domain->expiry_date = $domain->next_due_date;
    
                    $days = @$billingSetting->create_domain_invoice_days;
                    if(!$days){
                        $days = $billingSetting->create_default_invoice_days;
                    }
    
                    $domain->ns1 = $cart->ns1;
                    $domain->ns2 = $cart->ns2;
                    $domain->ns3 = $cart->ns3;
                    $domain->ns4 = $cart->ns4;
    
                    $domain->next_invoice_date = Carbon::parse($domain->next_due_date)->subDays($days)->toDateTimeString();
                    $domain->reg_date = Carbon::now();
                    $domain->save();
                }

                $this->invoiceItemForDomain($invoice, $domain, $cart);
            }
            
        }

        $this->tax($invoice);
        $carts->each->delete();

        if($request->filled('payment')){
            return $this->processInvoicePayment($request, $invoice, $user);
        }

        $notify[] = ['success', 'Your order has been placed and your hosting service is active!'];
        return redirect()->route('user.service.list')->withNotify($notify);
    } 

    private function validatePaymentRequest(Request $request){
        $request->validate([
            'payment' => 'required',
        ]);

        if($request->payment != 'wallet'){
            $request->validate([
                'method_code' => 'required',
                'currency' => 'required',
            ]);
        }
    }

    private function walletPayableAmount($amount){
        $tax = gs('tax') ?? 0;

        if($tax <= 0){
            return $amount;
        }

        return $amount + ($amount * $tax / 100);
    }

    private function hostingConfigs($id, $data){
        $array = [];  
        
        foreach($data ?? [] as $optionId => $subOptionId){
            $array[] = [
                'hosting_id' => $id,
                'configurable_group_option_id' => $optionId,                            
                'configurable_group_sub_option_id' => $subOptionId,                            
            ];
        }
        
        HostingConfig::insert($array);
    }
 
    private function invoiceItemForHosting($invoice, $hosting, $product){
        $order = $invoice->order;
  
        //Insert new row for setup fee 
        if($hosting->setup_fee != 0){
            $item = new InvoiceItem();
            $item->invoice_id = $invoice->id;
            $item->user_id = $invoice->user_id;
            $item->hosting_id = $hosting->id;
            $item->description = $product->name.' '.'Setup Fee'."\n".$product->serviceCategory->name;
            $item->amount = $hosting->setup_fee;
            $item->trx_type = '+';
            $item->save();
        } 
      
        $domainText = $hosting->domain ? ' - ' .$hosting->domain : null; 
        $date = ' (One Time)';

        if($hosting->billing_cycle != 0){
            $date = ' ('.showDateTime($hosting->created_at, 'd/m/Y').' - '.showDateTime($hosting->next_due_date, 'd/m/Y') .')';
        }

        $text = $product->name . $domainText. $date ."\n".$product->serviceCategory->name;
       
        foreach($hosting->hostingConfigs as $config){
            $text = $text ."\n". $config->select->name.': '.$config->option->name;
        }

        //Insert new row for hosting/service 
        $item = new InvoiceItem(); 
        $item->invoice_id = $invoice->id;
        $item->user_id = $invoice->user_id;
        $item->hosting_id = $hosting->id;
        $item->description = $text;
        $item->trx_type = '+';
        $item->amount = $hosting->recurring_amount;
        $item->save();

        //Insert new row for discount 
        if($hosting->discount != 0){
            $item = new InvoiceItem();
            $item->invoice_id = $invoice->id;
            $item->user_id = $invoice->user_id;
            $item->hosting_id = $hosting->id;
            $item->description = 'Coupon Code: '.@$order->coupon->code.' '.$product->serviceCategory->name;
            $item->amount = '-'.$hosting->discount;
            $item->trx_type = '-';
            $item->save();
        }

    }

    private function invoiceItemForDomain($invoice, $domain, $cart){
    
        $order = $invoice->order;
        $cartType = $cart->type == 4;

        if($cartType){ //For renew 
            $domainText = ' - '. $domain->domain .' - '. $cart->reg_period . ' Year/s';

            $text = 'Domain Renew' . 
            $domainText. ' ('.showDateTime($domain->next_due_date,'d/m/Y').' - '.showDateTime(Carbon::parse($domain->next_due_date)->addYears($cart->reg_period), 'd/m/Y') .')';

            $couponText = 'Coupon Code: '.@$order->coupon->code.' for domain Renew';
        }
        else{ //For register 
            $protection = $domain->id_protection ? '+ ID Protection' : null;
            $domainText = ' - '. $domain->domain .' - '. $domain->reg_period . ' Year/s';

            $text = 'Domain Registration' . 
            $domainText. ' ('.showDateTime($domain->created_at, 'd/m/Y').' - '.showDateTime($domain->next_due_date, 'd/m/Y') .')'."\n".$protection;

            $couponText = 'Coupon Code: '.@$order->coupon->code.' for domain Registration';
        }
    
        $item = new InvoiceItem();
        $item->invoice_id = $invoice->id;
        $item->user_id = $invoice->user_id;
        $item->domain_id = $domain->id;
        $item->item_type = 1;
        $item->description = $text; 
        $item->trx_type = '+';
        
        if($cartType){
            $item->amount = $cart->total; 
            $item->reg_period = $cart->reg_period; 
            $item->recurring_amount = $cart->total; 
            $item->next_due_date = Carbon::parse($domain->next_due_date)->addYears($cart->reg_period); 
        }else{
            $item->amount = $domain->recurring_amount;
        }

        $item->save();

        if($cart->discount != 0){ //For discount 
            $item = new InvoiceItem();
            $item->invoice_id = $invoice->id;
            $item->user_id = $invoice->user_id;
            $item->domain_id = $domain->id;
            $item->description = $couponText;
            $item->amount = '-'.$cart->discount;
            $item->trx_type = '-';
            $item->save();
        }
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

    public function viewInvoice($id){

        $user = auth()->user();
        $pageTitle = 'Invoice';

        $invoice = Invoice::whereBelongsTo($user)->with('payments.gateway')->findOrFail($id);
        $items = $invoice->items->groupBy(['hosting_id', 'domain_id']);

        $address = Frontend::where('data_keys','invoice_address.content')->first();
        $order = $invoice->order;

        $gatewayCurrency = GatewayCurrency::whereHas('method', function ($gate){
            $gate->where('status', 1);
        })->with('method')->orderby('method_code')->get();
        
        return view('Template::user.invoice.view', compact('pageTitle', 'user', 'invoice', 'gatewayCurrency', 'address', 'order', 'items'));
    }

    public function payment(Request $request){
       
        $request->validate([
            'payment' => 'required',
            'invoice_id' => 'required|integer',
        ]);

        $user = auth()->user();
        $invoice = Invoice::unpaid()->whereBelongsTo($user)->findOrFail($request->invoice_id);

        return $this->processInvoicePayment($request, $invoice, $user);
    }

    private function processInvoicePayment(Request $request, Invoice $invoice, $user){

        if($request->payment == 'wallet' && !gs('deposit_module')){
            $notify[] = ['warning', 'You cannot use wallet balance right now'];
            return redirect()->route('user.invoice.view', $invoice->id)->withNotify($notify);
        }

        if($user->kv == Status::KYC_UNVERIFIED){
            $notify[] = ['error','You are not KYC verified. For being KYC verified, please provide these information'];
            return to_route('user.kyc.form')->withNotify($notify);
        }

        if($user->kv == Status::KYC_PENDING){
            $notify[] = ['warning','Your documents for KYC verification is under review. Please wait for admin approval'];
            return to_route('user.home')->withNotify($notify);
        }

        $order = $invoice->order;
        $amount = $invoice->amount;

        if($invoice->amount <= 0){
            $notify[] = ['info', 'Sorry, The invoice amount could not be zero or negative'];
            return redirect()->route('user.invoice.view', $invoice->id)->withNotify($notify);
        }

        if($request->payment == 'wallet'){
            $amount = $invoice->amount;
            
            if($amount > $user->balance){
                $notify[] = ['error', 'You\'ve no sufficient balance to payment'];
                return redirect()->route('user.invoice.view', $invoice->id)->withNotify($notify);
            }
        
            $afterPayment = new AfterPayment();
            $afterPayment->pay($invoice);

            $notify[] = ['success', 'Payment completed successfully! Your service is ready.'];
            return redirect()->route('user.service.list')->withNotify($notify); 
        }

        $this->validatePaymentRequest($request);

        $gate = GatewayCurrency::whereHas('method', function ($gate) {
            $gate->where('status', 1);
        })->where('method_code', $request->method_code)->where('currency', $request->currency)->first();

        if (!$gate) {
            $notify[] = ['error', 'Invalid gateway'];
            return redirect()->route('user.invoice.view', $invoice->id)->withNotify($notify);
        }

        if ($gate->min_amount > $amount || $gate->max_amount < $amount) {
            $notify[] = ['error', 'Please follow deposit limit'];
            return redirect()->route('user.invoice.view', $invoice->id)->withNotify($notify);
        }

        if($order){
            Deposit::where('order_id', $order->id)->where('status', 0)->delete();
        }
        
        $charge = $gate->fixed_charge + ($amount * $gate->percent_charge / 100);
        $payable = $amount + $charge;
        $final_amo = $payable * $gate->rate;
      
        $data = new Deposit();
        $data->order_id = @$order->id ?? 0;
        $data->invoice_id = $invoice->id;
        $data->user_id = $user->id;
        $data->method_code = $gate->method_code;
        $data->method_currency = strtoupper($gate->currency);
        $data->amount = $amount;
        $data->charge = $charge;
        $data->rate = $gate->rate;
        $data->final_amount = $final_amo;
        $data->btc_amount = 0;
        $data->btc_wallet = ''; 
        $data->trx = getTrx();

        $data->success_url = urlPath('user.invoice.view', $invoice->id);
        $data->failed_url = urlPath('user.invoice.view', $invoice->id);

        $data->payment_try = 0;
        $data->status = 0;
        $data->save();

        session()->put('Track', $data->trx);
        return redirect()->route('user.deposit.confirm');
    }

    public function list(){
        $pageTitle = 'Invoice List';
        $user = auth()->user();
        $invoices = Invoice::whereBelongsTo($user)->orderBy('id', 'DESC')->paginate(getPaginate());
        return view('Template::user.invoice.list', compact('pageTitle', 'invoices'));
    }
     
    public function download($id, $view = null){

        $user = auth()->user();
        $invoice = Invoice::whereBelongsTo($user)->findOrFail($id);
        $items = $invoice->items->groupBy(['hosting_id', 'domain_id']);

        $address = Frontend::where('data_keys','invoice_address.content')->first();
        $user = $invoice->user;
        $pageTitle = 'Invoice';

        $pdf = PDF::loadView('invoice', compact('pageTitle', 'invoice', 'user', 'address', 'items'));

        if($view){
            return $pdf->stream('invoice.pdf');
        }

        return $pdf->download('invoice.pdf');
    }

}
