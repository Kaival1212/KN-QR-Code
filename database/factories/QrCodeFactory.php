<?php

namespace Database\Factories;

use App\Models\QrCode;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<QrCode>
 */
class QrCodeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'name' => fake()->words(3, true),
            'slug' => fake()->unique()->regexify('[A-Za-z0-9]{8}'),
            'destination_url' => fake()->url(),
            'fallback_url' => fake()->optional(0.4)->url(),
            'is_active' => fake()->boolean(80),
            'scans_count' => fake()->numberBetween(0, 500),
        ];
    }

    public function active(): static
    {
        return $this->state(fn (array $attributes) => ['is_active' => true]);
    }

    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => ['is_active' => false]);
    }
}
