<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Facades\Auth;

class Hashtag extends Model
{
    protected $fillable = [
        'name',
        'user_id',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function todos(): BelongsToMany
    {
        return $this->belongsToMany(Todo::class);
    }

    public static function search(string $query): array
    {
        return static::where('user_id', Auth::id())
            ->where('name', 'like', "%{$query}%")
            ->pluck('name')
            ->toArray();
    }
}
