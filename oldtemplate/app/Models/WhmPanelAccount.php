<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class WhmPanelAccount extends Model
{
    protected $guarded = [];

    protected $casts = [
        'suspended_at' => 'datetime',
        'terminated_at' => 'datetime',
    ];

    public function node(): BelongsTo
    {
        return $this->belongsTo(WhmPanelNode::class, 'node_id');
    }

    public function hosting(): BelongsTo
    {
        return $this->belongsTo(Hosting::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function websites(): HasMany
    {
        return $this->hasMany(WhmPanelWebsite::class, 'account_id');
    }
}
