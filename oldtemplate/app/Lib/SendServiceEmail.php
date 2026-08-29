<?php

namespace App\Lib;

class SendServiceEmail{

    public static function orderNotify($invoice, $order){
        try {
            $user = $order->user;
            $itemsDescription = null;
            $items = $invoice->items()->where('trx_type', '+')->select('description')->get();

            foreach($items as $item){
                $itemsDescription .= $item->description.'<br>';
            }

            $itemsDescription = (str_replace("\n", "<br/>", $itemsDescription));

            notify($user, 'ORDER_NOTIFICATION', [
                'order_id'=>$order->id,
                'created_at'=>showDateTime($order->created_at),
                'invoice_id'=>$invoice->id,
                'amount'=>showAmount($invoice->amount, currencyFormat:false),
                'name'=>$user->fullname,
                'email'=>$user->email,
                'address'=>@$user->address->address,
                'city'=>@$user->address->city,
                'state'=>@$user->address->state,
                'zip'=>@$user->address->zip,
                'country'=>@$user->address->country,
                'phone'=>@$user->mobile,
                'invoice_link'=>route('user.invoice.view', $invoice->id),
                'client_ip'=>$order->ip_address,
                'order_items'=>$itemsDescription,
            ]);

            if (class_exists(\App\Services\TelegramService::class)) {
                \App\Services\TelegramService::notifyNewOrder($order, $items->pluck('description')->toArray());
            }
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning("Order notification warning: " . $e->getMessage());
        }
    } 

    public static function serviceNotify($hosting){
        try {
            $product = $hosting->product;
            $server = @$hosting->server;
            $act = welcomeEmail()[$product->welcome_email]['act'] ?? null; 
            $user = $hosting->user;

            if (class_exists(\App\Services\TelegramService::class)) {
                \App\Services\TelegramService::notifyServiceProvisioned($hosting);
            }

            if(!$act){
                return false;
            }

            if($act == 'HOSTING_ACCOUNT'){    
                notify($user, $act, [
                    'service_product_name' => $product->name,
                    'service_domain' => $hosting->domain,
                    'service_first_payment_amount' => showAmount($hosting->first_payment_amount, currencyFormat:false),
                    'service_recurring_amount' => showAmount($hosting->recurring_amount, currencyFormat:false),
                    'service_billing_cycle' => billingCycle(@$hosting->billing_cycle, true)['showText'],
                    'service_next_due_date' => showDateTime($hosting->next_due_date, 'd/m/Y'),
                    'service_username' => $hosting->username,
                    'service_password' => $hosting->password,
                    'ns1' => @$server->ns1,
                    'ns2' => @$server->ns2,
                    'ns3' => @$server->ns3,
                    'ns4' => @$server->ns4,
                    'ns1_ip' => @$server->ns1_ip,
                    'ns2_ip' => @$server->ns2_ip,
                    'ns3_ip' => @$server->ns3_ip,
                    'ns4_ip' => @$server->ns4_ip,
                    'service_server_ip' => @$server->ip_address,
                ]);
            }
            elseif($act == 'RESELLER_ACCOUNT'){
                notify($user, $act, [
                    'service_domain' => $hosting->domain,
                    'service_username' => $hosting->username,
                    'service_password' => $hosting->password, 
                    'service_product_name' => $product->name,
                    'service_server_ip' => $hosting->dedicated_ip,
                ]);
            }
            elseif($act == 'VPS_SERVER'){
                notify($user, $act, [
                    'service_product_name' => $product->name,
                    'service_dedicated_ip' => $hosting->dedicated_ip,
                    'service_password' => $hosting->password, 
                    'service_assigned_ips' => $hosting->assigned_ips,
                    'service_domain' => $hosting->domain,
                    'ns1' => @$server->ns1,
                    'ns2' => @$server->ns2,
                ]);
            }
            elseif($act == 'OTHER_PRODUCT'){
                notify($user, $act, [
                    'service_product_name' => $product->name,
                    'service_recurring_amount' => showAmount($hosting->recurring_amount, currencyFormat:false),
                    'service_billing_cycle' => billingCycle(@$hosting->billing_cycle, true)['showText'],
                    'service_next_due_date' => showDateTime($hosting->next_due_date, 'd/m/Y')
                ]);
            }
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning("Service notification warning: " . $e->getMessage());
        }
    }

    public static function domainNotify($domain){
        try {
            notify($domain->user, 'DOMAIN_REGISTER', [
                'domain_name' => $domain->domain,
                'domain_reg_date' => showDateTime($domain->reg_date, 'd/m/Y'),
                'domain_reg_period' => $domain->reg_period, 
                'first_payment_amount' => showAmount($domain->first_payment_amount, currencyFormat:false),
                'next_due_date' => showDateTime($domain->next_due_date, 'd/m/Y')
            ]);
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning("Domain notification warning: " . $e->getMessage());
        }
    }

    public static function domainRenewNotify($domain){
        try {
            notify($domain->user, 'DOMAIN_RENEW_NOTIFICATION', [
                'domain'=>$domain->domain,
                'reg_period'=>$domain->reg_period,
                'recurring_amount'=>showAmount($domain->recurring_amount, currencyFormat:false),
                'next_due_date'=>showDateTime($domain->next_due_date, 'd/m/Y'),
            ]);
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning("Domain renew notification warning: " . $e->getMessage());
        }
    }

    public static function invoicePaymentReminder($invoice){
        try {
            notify($invoice->user, 'INVOICE_PAYMENT_REMINDER', [
                'invoice_number' => $invoice->id,
                'invoice_created' => showDatetime($invoice->created, 'd/m/Y'),
                'invoice_due_date' => showDatetime($invoice->due_date, 'd/m/Y'),
                'invoice_link' => route('user.invoice.view', $invoice->id),
            ]); 
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning("Invoice reminder warning: " . $e->getMessage());
        }
    }

    public static function firstInvoiceOverdue($invoice){
        try {
            notify($invoice->user, 'FIRST_INVOICE_OVERDUE_NOTICE', [
                'invoice_number' => $invoice->id,
                'invoice_created' => showDatetime($invoice->created, 'd/m/Y'),
                'invoice_due_date' => showDatetime($invoice->due_date, 'd/m/Y'),
                'invoice_link' => route('user.invoice.view', $invoice->id),
            ]); 
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning("First overdue notice warning: " . $e->getMessage());
        }
    }

    public static function secondInvoiceOverdue($invoice){
        try {
            notify($invoice->user, 'SECOND_INVOICE_OVERDUE_NOTICE', [
                'invoice_number' => $invoice->id,
                'invoice_created' => showDatetime($invoice->created, 'd/m/Y'),
                'invoice_due_date' => showDatetime($invoice->due_date, 'd/m/Y'),
                'invoice_link' => route('user.invoice.view', $invoice->id),
            ]);
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning("Second overdue notice warning: " . $e->getMessage());
        }
    }

    public static function thirdInvoiceOverdue($invoice){
        try {
            notify($invoice->user, 'THIRD_INVOICE_OVERDUE_NOTICE', [
                'invoice_number' => $invoice->id,
                'invoice_created' => showDatetime($invoice->created, 'd/m/Y'),
                'invoice_due_date' => showDatetime($invoice->due_date, 'd/m/Y'),
                'invoice_link' => route('user.invoice.view', $invoice->id),
            ]); 
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning("Third overdue notice warning: " . $e->getMessage());
        }
    }

    public static function serviceSuspend($hosting, $request){
        try {
            $user = $hosting->user;
            $product = $hosting->product;

            notify($user, 'SERVICE_SUSPEND', [
                'service_name' => $product->name,
                'service_next_due_date' => showDateTime($hosting->next_due_date, 'd/m/Y'),
                'service_suspension_reason' => $request->suspend_reason,
            ]);
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning("Service suspend notification warning: " . $e->getMessage());
        }
    }

    public static function serviceUnsuspend($hosting){
        try {
            $user = $hosting->user;
            $product = $hosting->product;

            notify($user, 'SERVICE_UNSUSPEND', [
                'service_name' => $product->name,
                'service_next_due_date' => showDateTime($hosting->next_due_date, 'd/m/Y')
            ]);
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning("Service unsuspend notification warning: " . $e->getMessage());
        }
    }

    public static function serviceExpiryReminder($hosting, int $daysRemaining){
        try {
            $user = $hosting->user;
            $product = $hosting->product;
            $invoice = \App\Models\Invoice::where('hosting_id', $hosting->id)->where('status', 2)->latest()->first();

            $invoiceLink = $invoice ? route('user.invoice.view', $invoice->id) : route('user.invoice.list');

            notify($user, 'INVOICE_PAYMENT_REMINDER', [
                'invoice_number' => $invoice ? $invoice->id : 'Renew-' . $hosting->id,
                'invoice_created' => showDateTime($hosting->reg_date, 'd/m/Y'),
                'invoice_due_date' => showDateTime($hosting->next_due_date, 'd/m/Y'),
                'amount' => showAmount($hosting->recurring_amount, currencyFormat: false),
                'currency_symbol' => gs('cur_sym'),
                'service_name' => $product->name,
                'service_domain' => $hosting->domain,
                'days_left' => $daysRemaining,
                'due_date' => showDateTime($hosting->next_due_date, 'd/m/Y'),
                'invoice_link' => $invoiceLink,
            ]);
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning("Service expiry reminder warning: " . $e->getMessage());
        }
    }

    public static function invoiceRefund($invoice, $refundAmount, $trx){
        try {
            notify($invoice->user, 'INVOICE_REFUND_NOTIFICATION', [
                'invoice_id' => $invoice->id,
                'refund_amount' => showAmount($refundAmount, currencyFormat:false),
                'trx_id' => $trx
            ]);
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning("Invoice refund notification warning: " . $e->getMessage());
        }
    }
}
