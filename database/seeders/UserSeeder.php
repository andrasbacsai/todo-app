<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $userExists = User::where('email', 'test@example.com')->exists();
        if (! $userExists) {
            User::factory()->create([
                'name' => 'Test User',
                'email' => 'test@example.com',
                'is_root_user' => true,
            ]);
        }
    }
}
