<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WhmPanelSsoToken extends Model
{
    protected $guarded = [];

    protected $casts = [
        'expires_at' => 'datetime',
        'used_at' => 'datetime',
    ];

    public function account(): BelongsTo
    {
        return $this->belongsTo(WhmPanelAccount::class, 'account_id');
    }
}
