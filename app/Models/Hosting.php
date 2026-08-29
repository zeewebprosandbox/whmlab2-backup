<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Hosting extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [ 
        'next_due_date'=>'date', 
        'next_invoice_date'=>'date', 
        'suspend_date'=>'date',  
        'termination_date'=>'date',
        'reg_date'=>'date',
        'last_cron'=>'datetime'
    ];

    protected static function booted()
    {
        static::deleting(function ($hosting) {
            // Clean up WhmPanel accounts and websites to prevent foreign key errors
            if (\Illuminate\Support\Facades\Schema::hasTable('whm_panel_accounts')) {
                $accounts = \App\Models\WhmPanelAccount::where('hosting_id', $hosting->id)->get();
                foreach ($accounts as $acc) {
                    if (\Illuminate\Support\Facades\Schema::hasTable('whm_panel_websites')) {
                        $websites = \App\Models\WhmPanelWebsite::where('account_id', $acc->id)->get();
                        foreach ($websites as $w) {
                            if (\Illuminate\Support\Facades\Schema::hasTable('whm_panel_dns_records')) {
                                \App\Models\WhmPanelDnsRecord::where('website_id', $w->id)->delete();
                            }
                            $w->delete();
                        }
                    }
                    if (\Illuminate\Support\Facades\Schema::hasTable('whm_panel_databases')) {
                        \App\Models\WhmPanelDatabase::where('account_id', $acc->id)->delete();
                    }
                    if (\Illuminate\Support\Facades\Schema::hasTable('whm_panel_mail_accounts')) {
                        \App\Models\WhmPanelMailAccount::where('account_id', $acc->id)->delete();
                    }
                    $acc->delete();
                }
            }

            // Clean up configs, cancel requests, and invoice relations
            \App\Models\HostingConfig::where('hosting_id', $hosting->id)->delete();
            \App\Models\CancelRequest::where('hosting_id', $hosting->id)->delete();
            \App\Models\InvoiceItem::where('hosting_id', $hosting->id)->delete();
            \App\Models\Invoice::where('hosting_id', $hosting->id)->update(['hosting_id' => null]);
        });
    }

    public function deleteWithRelations(): bool
    {
        if ($this->server && $this->server->current_accounts > 0) {
            $this->server->decrement('current_accounts');
        }

        return (bool) $this->delete();
    }

    public function scopeActive($query){
        return $query->where('status', 1);
    }

    public function user(){
        return $this->belongsTo(User::class)->withDefault();
    }

    public function product(){
        return $this->belongsTo(Product::class)->withDefault();
    }

    public function deposit(){ 
        return $this->belongsTo(Deposit::class, 'deposit_id');
    }
  
    public function server(){
        return $this->belongsTo(Server::class, 'server_id');
    }

    public function hostingConfigs(){
        return $this->hasMany(HostingConfig::class, 'hosting_id');
    }
 
    public function order(){
        return $this->belongsTo(Order::class, 'order_id');
    }

    public function cancelRequest(){
        return $this->hasOne(CancelRequest::class, 'hosting_id');
    }

    public function invoices(){
        return $this->hasMany(Invoice::class, 'hosting_id');
    }

    public function details(){
        return $this->hasOne(InvoiceItem::class);
    }

    public function showStatus(): Attribute{
    
        return new Attribute(
            get: function(){
           
                if(request()->routeIs('admin*')){
                    $class = "text--small badge font-weight-normal badge--";
                }else{
                    $class = "badge badge--";
                }
        
                $text = 'N/A'; 
        
                if ($this->status == 1){
                    $class .= 'success';
                    $text = Self::status()[1];
                } 
                if ($this->status == 2){ 
                    $class .= 'danger';
                    $text = Self::status()[2];
                }
                elseif ($this->status == 3){
                    $class .= 'warning';
                    $text = Self::status()[3];
                }
                elseif ($this->status == 4){
                    $class .= 'dark';
                    $text = Self::status()[4];
                }
                elseif ($this->status == 5){
                    $class .= 'warning';
                    $text = Self::status()[5];
                }
                 
                return "<span class='$class'>" . trans($text) . "</span>";
            }
        );
    }

    public static function status($implode = false){

        $status = [
            1=> trans('Active'), 
            2=> trans('Pending'), 
            3=> trans('Suspended'),
            4=> trans('Terminated'), 
            5=> trans('Cancelled'),
        ]; 

        if($implode){
            return implode(',', array_keys($status));
        }

        return $status;
    }

}
