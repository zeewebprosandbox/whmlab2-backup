<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class WhmPanelNode extends Model
{
    protected $guarded = [];

    protected $hidden = [
        'api_token',
    ];

    protected $casts = [
        'last_sync_at' => 'datetime',
    ];

    public function server(): BelongsTo
    {
        return $this->belongsTo(Server::class);
    }

    public function accounts(): HasMany
    {
        return $this->hasMany(WhmPanelAccount::class, 'node_id');
    }
}
