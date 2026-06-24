<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NewsletterConversion extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'newsletter_subscription_id',
        'product_order_id',
        'product_id',
        'amount',
        'status',
        'converted_at',
    ];

    /**
     * The attribute casting configuration.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'converted_at' => 'datetime',
    ];

    /**
     * Subscription that generated the conversion.
     */
    public function subscription(): BelongsTo
    {
        return $this->belongsTo(NewsletterSubscription::class, 'newsletter_subscription_id');
    }

    /**
     * Paid order attached to this conversion.
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(ProductOrder::class, 'product_order_id');
    }

    /**
     * Product attached to the conversion.
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
