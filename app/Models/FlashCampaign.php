<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

class FlashCampaign extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'title',
        'message',
        'discount_code',
        'cta_label',
        'cta_url',
        'audience',
        'is_active',
        'starts_at',
        'ends_at',
        'created_by_user_id',
        'last_sent_at',
        'impressions_count',
    ];

    /**
     * The attribute casting configuration.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_active' => 'boolean',
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
        'last_sent_at' => 'datetime',
    ];

    /**
     * User that created the campaign.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by_user_id');
    }

    /**
     * Scope a query to campaigns currently active.
     */
    public function scopeActiveNow(Builder $query, ?Carbon $moment = null): Builder
    {
        $moment ??= now();

        return $query
            ->where('is_active', true)
            ->where(function (Builder $builder) use ($moment): void {
                $builder
                    ->whereNull('starts_at')
                    ->orWhere('starts_at', '<=', $moment);
            })
            ->where(function (Builder $builder) use ($moment): void {
                $builder
                    ->whereNull('ends_at')
                    ->orWhere('ends_at', '>=', $moment);
            });
    }
}
