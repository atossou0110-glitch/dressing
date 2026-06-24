<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UgcSubmission extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'product_id',
        'author_name',
        'author_city',
        'author_email',
        'caption',
        'photo_path',
        'status',
        'visitor_hash',
        'ip_hash',
        'approved_at',
        'rejected_at',
        'featured_at',
        'admin_notes',
    ];

    /**
     * The attribute casting configuration.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'approved_at' => 'datetime',
        'rejected_at' => 'datetime',
        'featured_at' => 'datetime',
    ];

    /**
     * Product associated with the submission.
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Resolve the public URL for the uploaded photo.
     */
    public function photoUrl(): string
    {
        return asset($this->photo_path);
    }
}
