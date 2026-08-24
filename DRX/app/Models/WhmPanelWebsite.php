<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class WhmPanelWebsite extends Model
{
    protected $guarded = [];

    protected $casts = [
        'ssl_enabled' => 'boolean',
    ];

    public function account(): BelongsTo
    {
        return $this->belongsTo(WhmPanelAccount::class, 'account_id');
    }

    public function dnsRecords(): HasMany
    {
        return $this->hasMany(WhmPanelDnsRecord::class, 'website_id');
    }
}
