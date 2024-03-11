<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            PeopleRobotsSeeder::class,
        ]);

        if (app()->environment('local')) {
            $this->createTestUser();
        }
    }

    protected function createTestUser(): void
    {
        if (User::firstWhere('email', 'test@example.com')) {
            return;
        }

        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => Hash::make('password'),
        ]);
    }
}
