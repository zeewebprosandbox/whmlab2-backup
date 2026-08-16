<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WhmPanelDnsRecord extends Model
{
    protected $guarded = [];

    public function website(): BelongsTo
    {
        return $this->belongsTo(WhmPanelWebsite::class, 'website_id');
    }
}
