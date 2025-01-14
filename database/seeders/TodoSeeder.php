<?php

namespace Database\Seeders;

use App\Models\Todo;
use Illuminate\Database\Seeder;

class TodoSeeder extends Seeder
{
    public function run(): void
    {
        for ($i = 0; $i < 10; $i++) {
            Todo::create([
                'title' => 'Test todo',
                'status' => 'backlog',
                'user_id' => 1,
                'worked_at' => null,
            ]);
        }
        for ($i = 0; $i < 3; $i++) {
            Todo::create([
                'title' => 'Todo from yesterday',
                'status' => 'backlog',
                'user_id' => 1,
                'worked_at' => now()->subDays(1),
            ]);
        }
        for ($i = 0; $i < 3; $i++) {
            Todo::create([
                'title' => 'Must done today',
                'status' => 'backlog',
                'user_id' => 1,
                'worked_at' => now(),
            ]);
        }

    }
}
