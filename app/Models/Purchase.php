<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Purchase extends Model
{
    protected $fillable = [
        'user_id',
        'stripe_id',
        'stripe_price',
        'stripe_status',
        'stripe_payment_intent',
        'quantity',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function active(): bool
    {
        return $this->stripe_status === 'paid';
    }
}
