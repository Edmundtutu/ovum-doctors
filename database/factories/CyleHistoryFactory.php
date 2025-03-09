<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\CyleHistory>
 */
class CyleHistoryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'patient_id' => $this->faker->numberBetween(1, 5),
            'month' => $this->faker->monthName,
            'cycle_length' => $this->faker->numberBetween(21, 35),
            'period_length' => $this->faker->numberBetween(3, 7),
            'symptoms' => $this->faker->words(3),
        ];
    }
}
