<?php

namespace Database\Factories;

use App\Models\Doctor;
use App\Models\Patient;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Patient>
 */
class PatientFactory extends Factory
{
    protected $model = Patient::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $medical_conditions = [
            'Polycystic Ovary Syndrome (PCOS)',
            'Endometriosis',
            'Irregular Menstruation',
            'None',
            'Hypothyroidism'
        ];

        $blood_types = ['A+', 'A-', 'B+', 'B-', 'O+', 'O-', 'AB+', 'AB-'];

        return [
            'doctor_id' => Doctor::factory(),
            'name' => fake()->name('female'),
            'date_of_birth' => fake()->dateTimeBetween('-40 years', '-18 years'),
            'email' => fake()->unique()->safeEmail(),
            'phone' => fake()->phoneNumber(),
            'address' => fake()->address(),
            'medical_condition' => fake()->randomElement($medical_conditions),
            'blood_type' => fake()->randomElement($blood_types),
            'emergency_contact' => fake()->name(),
            'emergency_phone' => fake()->phoneNumber(),
        ];
    }
}
