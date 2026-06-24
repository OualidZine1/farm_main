<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\InventoryTransaction>
 */
class InventoryTransactionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'product_id' => \App\Models\Product::factory(),
            'user_id' => fn () => \App\Models\User::inRandomOrder()->first()?->id ?? \App\Models\User::factory()->create()->id,
            // Ensure used_by_user_id defaults to a valid random user id (fallback to creating one)
            'used_by_user_id' => fn () => \App\Models\User::inRandomOrder()->first()?->id ?? \App\Models\User::factory()->create()->id,
            'type' => $this->faker->randomElement(['in', 'out']),
            'quantity' => $this->faker->numberBetween(1, 100),
            'field_id' => \App\Models\Field::factory(),
            'date' => $this->faker->dateTimeBetween('-3 years', 'now'),
            'notes' => $this->faker->sentence(),
            'price' => $this->faker->randomFloat(2, 10, 500),
        ];
    }

    public function incoming(): Factory
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'in',
            'field_id' => null,
            'used_by_user_id' => null,
        ]);
    }

    public function outgoing(): Factory
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'out',
        ]);
    }
}
