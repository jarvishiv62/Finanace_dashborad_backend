<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class FinancialRecordFactory extends Factory
{
    private array $categories = [
        'salary',
        'rent',
        'food',
        'investment',
        'utilities',
        'transport',
        'healthcare',
        'education',
        'entertainment',
        'freelance',
    ];

    public function definition(): array
    {
        $type = fake()->randomElement(['income', 'expense']);

        return [
            'user_id' => User::factory(),
            'amount' => fake()->randomFloat(2, 100, 50000),
            'type' => $type,
            'category' => fake()->randomElement($this->categories),
            'date' => fake()->dateTimeBetween('-6 months', 'now')->format('Y-m-d'),
            'notes' => fake()->optional(0.7)->sentence(),
        ];
    }

    public function income(): static
    {
        return $this->state(['type' => 'income']);
    }

    public function expense(): static
    {
        return $this->state(['type' => 'expense']);
    }
}