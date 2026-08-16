<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use App\Traits\GlobalStatus;

class Server extends Model{

    use HasFactory, GlobalStatus;

    protected $casts = [
        'health_checked_at' => 'datetime',
    ];

    public function group(){
        return $this->belongsTo(ServerGroup::class, 'server_group_id');
    }

    public function hostings()
    {
        return $this->hasMany(Hosting::class, 'server_id');
    }

    public function scopeActive($query = null){

        if($query){
            $query = $query;
        }else{
            $query = $this;
        }

        return $query->where('status', 1);
    }

    public function scopeProvisionable($query)
    {
        return $query->active()
            ->where(function ($server) {
                $server->where('max_accounts', 0)
                    ->orWhereColumn('current_accounts', '<', 'max_accounts');
            })
            ->where(function ($server) {
                $server->whereIn('health_status', ['online', 'healthy', 'unknown'])
                    ->orWhereNull('health_status');
            });
    }

    public static function serviceRoles(): array
    {
        return [
            'any' => 'Any Service',
            'shared' => 'Shared Hosting',
            'premium_shared' => 'Premium Shared Hosting',
            'vps' => 'VPS / KVM VPS',
            'dedicated' => 'Dedicated Server',
            'mail' => 'Mail Services',
            'rdp' => 'Remote Desktop',
            'radio' => 'Radio Shoutcast',
            'domain' => 'Domain / DNS',
        ];
    }

    public static function roleForProduct(?Product $product): string
    {
        if (!$product) {
            return 'any';
        }

        $name = strtolower(($product->name ?? '') . ' ' . optional($product->serviceCategory)->name);

        return match (true) {
            str_contains($name, 'mail'), str_contains($name, 'email') => 'mail',
            str_contains($name, 'radio'), str_contains($name, 'shoutcast') => 'radio',
            str_contains($name, 'rdp'), str_contains($name, 'remote desktop') => 'rdp',
            str_contains($name, 'dedicated') => 'dedicated',
            str_contains($name, 'vps'), str_contains($name, 'kvm'), str_contains($name, 'nvme') => 'vps',
            str_contains($name, 'premium') => 'premium_shared',
            str_contains($name, 'domain'), str_contains($name, 'dns') => 'domain',
            default => $product->product_type == 3 ? 'vps' : 'shared',
        };
    }

    public static function bestForProduct(Product $product): ?self
    {
        if (!$product->server_group_id) {
            return null;
        }

        $role = self::roleForProduct($product);

        return self::where('server_group_id', $product->server_group_id)
            ->provisionable()
            ->where(function ($server) use ($role) {
                $server->where('service_role', $role)->orWhere('service_role', 'any');
            })
            ->orderByRaw('CASE WHEN service_role = ? THEN 0 ELSE 1 END', [$role])
            ->orderByRaw('CASE WHEN max_accounts > 0 THEN current_accounts / max_accounts ELSE 0 END asc')
            ->orderBy('current_accounts')
            ->first();
    }

    public function capacityPercent(): int
    {
        if (!$this->max_accounts) {
            return 0;
        }

        return min(100, (int) round(($this->current_accounts / max(1, $this->max_accounts)) * 100));
    }

    public function serviceRoleLabel(): string
    {
        return self::serviceRoles()[$this->service_role ?: 'any'] ?? ucfirst((string) $this->service_role);
    }

    public function healthBadge(): Attribute
    {
        return new Attribute(
            get: function () {
                $status = $this->health_status ?: 'unknown';
                $class = match ($status) {
                    'online', 'healthy' => 'success',
                    'warning' => 'warning',
                    'offline', 'error' => 'danger',
                    default => 'dark',
                };

                return "<span class='text--small badge font-weight-normal badge--$class'>" . trans(ucfirst($status)) . '</span>';
            }
        );
    }

    public function showStatus(): Attribute{
    
        return new Attribute(
            get: function(){
           
                if(request()->routeIs('admin*')){
                    $class = "text--small badge font-weight-normal badge--";
                }else{
                    $class = "badge badge-";
                }

                if ($this->status == 1){
                    $class .= 'success';
                    $text = trans('Enable');
                }
                else{
                    $class .= 'warning';
                    $text = trans('Disable');
                }
                
                return "<span class='$class'>" . trans($text) . "</span>";
            }
        );
    }

}
