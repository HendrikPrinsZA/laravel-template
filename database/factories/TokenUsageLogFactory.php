<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\TokenUsageLog>
 */
class TokenUsageLogFactory extends Factory
{
    public function definition(): array
    {
        return [
            'key' => $this->faker->word,
            'usage' => [],
            'tokens_used' => $this->faker->randomNumber(),
        ];
    }
}
