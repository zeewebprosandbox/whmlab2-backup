<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WhmPanelServiceItem extends Model
{
    protected $guarded = [];

    protected $casts = [
        'config' => 'array',
        'last_checked_at' => 'datetime',
    ];

    public function account(): BelongsTo
    {
        return $this->belongsTo(WhmPanelAccount::class, 'account_id');
    }

    public function website(): BelongsTo
    {
        return $this->belongsTo(WhmPanelWebsite::class, 'website_id');
    }
}
