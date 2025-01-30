<?php

namespace App\Http\Controllers;

use App\Models\Todo;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index()
    {
        return Inertia::render('Dashboard', [
            'user' => auth()->user(),
            'todos' => Inertia::defer(fn () => Todo::getTodayTodos()),
        ]);

    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => ['required', 'max:50'],
        ]);
        $validated['user_id'] = auth()->user()->id;
        $validated['worked_at'] = now();
        Todo::create($validated);

        return to_route('dashboard.index');
    }
}
