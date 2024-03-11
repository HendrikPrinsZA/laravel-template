<?php

namespace Database\Seeders;

use App\Enums\PersonType;
use App\Models\Person;
use Illuminate\Database\Seeder;

class PeopleRobotsSeeder extends Seeder
{
    public function run(): void
    {
        Person::query()->upsert([
            'name' => 'Robot A',
            'type' => PersonType::ROBOT->value,
            'email' => config('spambot.username', 'robot-a@fake.com'),
        ], ['email'], ['name', 'type', 'email']);
    }
}
