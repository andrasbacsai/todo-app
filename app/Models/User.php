<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Log;
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

        static::created(function (User $user) {
            try {
                Todo::create([
                    'title' => 'Welcome to the app!',
                    'worked_at' => now(),
                    'user_id' => $user->id,
                ]);
                Todo::create([
                    'title' => 'You can use hashtags to group your todos. #importantproject',
                    'worked_at' => now(),
                    'user_id' => $user->id,
                ]);
                Todo::create([
                    'title' => 'Click on the dropdown icons to see other todos. 👇',
                    'worked_at' => now(),
                    'user_id' => $user->id,
                ]);
                Todo::create([
                    'worked_at' => now()->subDay(),
                    'title' => 'This should be done yesterday. Click on me to move it to today.',
                    'user_id' => $user->id,
                ]);
                Todo::create([
                    'title' => 'This is done. Well done!',
                    'status' => 'completed',
                    'worked_at' => now(),
                    'user_id' => $user->id,
                ]);
                Todo::create([
                    'title' => 'Should be done someday. Maybe today?',
                    'worked_at' => null,
                    'user_id' => $user->id,
                ]);
            } catch (\Exception $e) {
                Log::error('Failed to create todos', [
                    'user_id' => $user->id,
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString(),
                ]);
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

    public function todos(): HasMany
    {
        return $this->hasMany(Todo::class);
    }
}
