<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SupportConversation extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'visitor_hash',
        'source_product_id',
        'customer_name',
        'customer_email',
        'source_path',
        'status',
        'needs_human',
        'last_message_at',
        'last_user_message_at',
        'last_assistant_message_at',
        'meta',
    ];

    /**
     * The attribute casting configuration.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'needs_human' => 'boolean',
        'last_message_at' => 'datetime',
        'last_user_message_at' => 'datetime',
        'last_assistant_message_at' => 'datetime',
        'meta' => 'array',
    ];

    /**
     * Product context attached to the conversation.
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'source_product_id');
    }

    /**
     * Messages exchanged in the conversation.
     */
    public function messages(): HasMany
    {
        return $this->hasMany(SupportMessage::class)->orderBy('id');
    }
}
