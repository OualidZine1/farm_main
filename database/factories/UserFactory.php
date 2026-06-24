<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'firstname' => $this->faker->firstName(),
            'lastname' => $this->faker->lastName(),
            'email' => $this->faker->unique()->safeEmail(),
            'password' => \Illuminate\Support\Facades\Hash::make('password'),
            'role' => $this->faker->randomElement(['administrator', 'manager']),
            'cin' => $this->faker->unique()->bothify('??######'),
        ];
    }

    public function manager(): Factory
    {
        return $this->state(fn (array $attributes) => [
            'role' => 'manager',
        ]);
    }

    public function administrator(): Factory
    {
        return $this->state(fn (array $attributes) => [
            'role' => 'administrator',
        ]);
    }
}
