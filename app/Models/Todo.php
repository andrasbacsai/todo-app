<?php

namespace App\Models;

use App\Events\TodoUpdated;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Facades\Auth;

class Todo extends Model
{
    use HasFactory;

    const MAX_DESCRIPTION_LENGTH = 10000; // 10KB limit for markdown content

    protected $fillable = ['title', 'status', 'description', 'user_id', 'worked_at'];

    protected $with = ['hashtags'];

    protected static function boot()
    {
        parent::boot();
        static::updated(function () {
            if (Auth::check()) {
                broadcast(new TodoUpdated(Auth::user()->id))->toOthers();
            }
        });
        static::creating(function ($todo) {
            $todo->user_id = Auth::user()->id;
        });
        static::created(function () {
            if (Auth::check()) {
                broadcast(new TodoUpdated(Auth::user()->id))->toOthers();
            }
        });
        static::deleted(function ($todo) {
            if (Auth::check()) {
                broadcast(new TodoUpdated(Auth::user()->id))->toOthers();
            }

            // Get the hashtags associated with this todo
            $hashtags = $todo->hashtags;

            // For each hashtag, check if it's used by any other todo
            foreach ($hashtags as $hashtag) {
                if ($hashtag->todos()->count() <= 1) { // 1 because the relationship still exists at this point
                    $hashtag->delete();
                }
            }
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function hashtags(): BelongsToMany
    {
        return $this->belongsToMany(Hashtag::class)->withTimestamps()->orderBy('name');
    }

    public static function regexHashtags(): string
    {
        return '/#[a-zA-Z0-9][a-zA-Z0-9\-_]*(\s|$)/';
    }

    public static function cleanTitle(string $title): string
    {
        return trim(preg_replace(self::regexHashtags(), '', $title));
    }

    public function syncHashtags(?string $title = null): void
    {
        if (! $title) {
            return;
        }

        preg_match_all('/#([\w\-]+)/', $title, $matches);
        $hashtags = $matches[1];

        // If no hashtags in the title and we're not in edit mode (no existing hashtags), just return
        if (empty($hashtags) && ! $this->exists) {
            return;
        }

        // If there are hashtags in the title, sync them
        if (! empty($hashtags)) {
            $existingHashtags = Hashtag::where('user_id', Auth::id())
                ->whereIn('name', $hashtags)
                ->get();

            $newHashtags = collect($hashtags)
                ->diff($existingHashtags->pluck('name'))
                ->map(fn ($name) => Hashtag::create([
                    'name' => $name,
                    'user_id' => Auth::id(),
                ]));

            // Get the new set of hashtag IDs
            $newHashtagIds = $existingHashtags->merge($newHashtags)->pluck('id')->toArray();

            // Perform the sync without detaching existing hashtags
            $this->hashtags()->syncWithoutDetaching($newHashtagIds);
        }
    }

    public static function getOwnTodo($id)
    {
        return self::where('user_id', Auth::id())->findOrFail($id);
    }

    public static function getAllTodos()
    {
        return self::where('user_id', Auth::id())->get();
    }

    public static function getAllTodosExceptToday()
    {
        return self::where('user_id', Auth::id())
            ->where(function ($query) {
                $query->where('worked_at', '<', now()->startOfDay())
                    ->orWhereNull('worked_at');
            })
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public static function getTodayTodos()
    {
        return self::where('user_id', Auth::id())->where('worked_at', '>=', now()->startOfDay())->where('worked_at', '<=', now()->endOfDay())->get();
    }

    public static function getBacklogTodos()
    {
        return self::where('user_id', Auth::id())->where('status', '!=', 'completed')->get();
    }

    public static function getYesterdayUndoneTodos()
    {
        return self::where('user_id', Auth::id())
            ->where(function ($query) {
                $query->where('worked_at', '>=', now()->subDay(1)->startOfDay())
                    ->where('worked_at', '<=', now()->subDay(1)->endOfDay());
            })
            ->where('status', '!=', 'completed')
            ->get()->sortBy('worked_at');
    }

    public static function updateTodo($id, $data)
    {
        $todo = self::where('user_id', Auth::id())->where('id', $id)->first();
        if (! $todo) {
            return false;
        }
        $todo->update($data);

        return true;
    }

    public static function transferUndoneYesterdayTodos()
    {
        $todos = self::getYesterdayUndoneTodos();
        foreach ($todos as $todo) {
            $todo->worked_at = now();
            $todo->save();
        }
    }

    // Add accessors and mutators for description
    protected function description(): \Illuminate\Database\Eloquent\Casts\Attribute
    {
        return \Illuminate\Database\Eloquent\Casts\Attribute::make(
            get: fn ($value) => $value ? base64_decode($value) : null,
            set: function ($value) {
                if ($value === null) {
                    return null;
                }
                if (strlen($value) > self::MAX_DESCRIPTION_LENGTH) {
                    throw new \Exception('Description is too long. Maximum length is '.self::MAX_DESCRIPTION_LENGTH.' characters.');
                }

                return base64_encode($value);
            }
        );
    }
}
