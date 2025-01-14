<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Cashier\Billable;

use function Illuminate\Events\queueable;

class User extends Authenticatable
{
    use Billable, HasFactory, Notifiable;

    protected static function booted(): void
    {
        static::creating(function (User $user) {
            if (blank($user->name)) {
                $user->name = str($user->email)->before('@')->value();
            }
        });
        static::updated(queueable(function (User $customer) {
            if ($customer->hasStripeId()) {
                $customer->syncStripeCustomerDetails();
            }
        }));
    }

    protected $fillable = [
        'name',
        'email',
        'password',
        'is_root_user',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_root_user' => 'boolean',
        ];
    }

    public function purchases(): HasMany
    {
        return $this->hasMany(Purchase::class);
    }

    public function purchase($priceId = null): ?Purchase
    {
        if ($priceId) {
            return $this->purchases()->where('stripe_price', $priceId)->first();
        }

        return $this->purchases()->latest()->first();
    }

    public function paid(): ?array
    {
        if ($this->is_root_user) {
            return [
                'type' => 'subscription',
                'name' => 'iddqd',
                'status' => 'active',
            ];
        }

        if ($this->subscribed()) {
            return [
                'type' => 'subscription',
                'name' => $this->subscription()->type,
                'status' => 'active',
            ];
        }

        $purchase = $this->purchase();
        if ($purchase && $purchase->active()) {
            return [
                'type' => 'purchase',
                'name' => 'one-time-purchase',
                'status' => 'paid',
            ];
        }

        return null;
    }
}
