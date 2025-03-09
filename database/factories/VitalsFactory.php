<?php

namespace Database\Factories;

use App\Models\Patient;
use App\Models\Vitals;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Vitals>
 */
class VitalsFactory extends Factory
{
    protected $model = Vitals::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'patient_id' => Patient::factory(),
            'blood_pressure' => fake()->numberBetween(90, 140) . '/' . fake()->numberBetween(60, 90),
            'heart_rate' => fake()->numberBetween(60, 100),
            'respiratory_rate' => fake()->numberBetween(12, 20),
            'temperature' => fake()->randomFloat(1, 36.1, 37.2),
            'weight' => fake()->randomFloat(1, 45, 100),
            'height' => fake()->randomFloat(1, 150, 180),
            'oxygen_saturation' => fake()->numberBetween(95, 100),
            'recorded_at' => fake()->dateTimeBetween('-6 months', 'now'),
        ];
    }
}
