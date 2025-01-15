<?php

namespace App\Models;

use App\Events\TodoUpdated;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Todo extends Model
{
    use HasFactory;

    protected $fillable = ['title', 'status', 'description', 'user_id', 'worked_at'];

    protected static function boot()
    {
        parent::boot();
        static::updated(function () {
            if (Auth::check()) {
                broadcast(new TodoUpdated(Auth::user()->id))->toOthers();
            }
        });
        static::created(function () {
            if (Auth::check()) {
                broadcast(new TodoUpdated(Auth::user()->id))->toOthers();
            }
        });
        static::deleted(function () {
            if (Auth::check()) {
                broadcast(new TodoUpdated(Auth::user()->id))->toOthers();
            }
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public static function getAllTodos()
    {
        return self::where('user_id', auth()->user()->id)->get();
    }

    public static function getAllTodosExceptToday()
    {
        return self::where('user_id', auth()->user()->id)
            ->where(function ($query) {
                $query->where('worked_at', '<', now()->startOfDay())
                    ->orWhereNull('worked_at');
            })
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public static function getTodayTodos()
    {
        return self::where('user_id', auth()->user()->id)->where('worked_at', '>=', now()->startOfDay())->where('worked_at', '<=', now()->endOfDay())->get();
    }

    public static function getBacklogTodos()
    {
        return self::where('user_id', auth()->user()->id)->where('status', '!=', 'completed')->get();
    }

    public static function getYesterdayUndoneTodos()
    {
        return self::where('user_id', auth()->user()->id)
            ->where(function ($query) {
                $query->where('worked_at', '>=', now()->subDay(1)->startOfDay())
                    ->where('worked_at', '<=', now()->subDay(1)->endOfDay());
            })
            ->where('status', '!=', 'completed')
            ->get()->sortBy('worked_at');
    }

    public static function updateTodo($id, $data)
    {
        $todo = self::where('user_id', auth()->user()->id)->where('id', $id)->first();
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
}
