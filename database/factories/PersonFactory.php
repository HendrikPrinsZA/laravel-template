<?php

namespace Database\Factories;

use App\Enums\PersonType;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Person>
 */
class PersonFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'type' => PersonType::HUMAN,
            'email' => fake()->unique()->safeEmail(),
        ];
    }
}
