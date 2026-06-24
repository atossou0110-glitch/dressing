<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BrowserNotificationSubscription extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'visitor_hash',
        'email',
        'permission',
        'subscribed_at',
        'last_seen_at',
        'last_notified_campaign_id',
        'last_notified_at',
        'meta',
    ];

    /**
     * The attribute casting configuration.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'subscribed_at' => 'datetime',
        'last_seen_at' => 'datetime',
        'last_notified_at' => 'datetime',
        'meta' => 'array',
    ];

    /**
     * Last campaign notified to the browser.
     */
    public function lastNotifiedCampaign(): BelongsTo
    {
        return $this->belongsTo(FlashCampaign::class, 'last_notified_campaign_id');
    }
}
