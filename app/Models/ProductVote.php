<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductVote extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'visitor_hash',
    ];

    /**
     * Product that owns the vote.
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
