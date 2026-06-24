<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class NewsletterSubscription extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'email',
        'name',
        'visitor_hash',
        'source_product_id',
        'source_page',
        'source_path',
        'discount_code',
        'status',
        'subscribed_at',
        'marketing_opt_in_at',
        'last_seen_at',
        'conversion_orders_count',
        'converted_revenue_total',
        'last_converted_at',
        'meta',
    ];

    /**
     * The attribute casting configuration.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'subscribed_at' => 'datetime',
        'marketing_opt_in_at' => 'datetime',
        'last_seen_at' => 'datetime',
        'last_converted_at' => 'datetime',
        'meta' => 'array',
    ];

    /**
     * Product context that generated the subscription.
     */
    public function sourceProduct(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'source_product_id');
    }

    /**
     * Conversions attributed to the subscription.
     */
    public function conversions(): HasMany
    {
        return $this->hasMany(NewsletterConversion::class);
    }
}
