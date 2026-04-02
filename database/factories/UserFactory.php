<?php

namespace Database\Factories;

use App\Enums\RoleEnum;
use App\Enums\StatusEnum;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class UserFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => bcrypt('password'),
            'remember_token' => Str::random(10),
            'role' => RoleEnum::Viewer,
            'status' => StatusEnum::Active,
        ];
    }

    public function admin(): static
    {
        return $this->state(['role' => RoleEnum::Admin]);
    }

    public function analyst(): static
    {
        return $this->state(['role' => RoleEnum::Analyst]);
    }

    public function viewer(): static
    {
        return $this->state(['role' => RoleEnum::Viewer]);
    }

    public function inactive(): static
    {
        return $this->state(['status' => StatusEnum::Inactive]);
    }
}