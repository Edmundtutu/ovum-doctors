<?php

namespace Database\Factories;

use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\Patient;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Appointment>
 */
class AppointmentFactory extends Factory
{
    protected $model = Appointment::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $appointmentTypes = [
            'Regular Checkup',
            'Consultation',
            'Ultrasound',
            'Follow-up',
            'Fertility Treatment'
        ];

        $status = [
            'scheduled',
            'completed',
            'cancelled',
            'rescheduled'
        ];

        // Generate a random future date (next 30 days) during business hours
        $date = fake()->dateTimeBetween('now', '+30 days');
        $hour = fake()->numberBetween(9, 16); // 9 AM to 4 PM
        $startTime = Carbon::instance($date)->setHour($hour)->setMinute(0)->setSecond(0);
        $endTime = (clone $startTime)->addMinutes(45); // 45-minute appointments

        return [
            'doctor_id' => Doctor::factory(),
            'patient_id' => Patient::factory(),
            'appointment_date' => $startTime->toDateString(),
            'start_time' => $startTime,
            'end_time' => $endTime,
            'type' => fake()->randomElement($appointmentTypes),
            'status' => fake()->randomElement($status),
            'notes' => fake()->optional(0.7)->sentence(),
            'reason' => fake()->sentence(),
        ];
    }

    /**
     * Configure the factory to generate completed past appointments.
     */
    public function past(): Factory
    {
        return $this->state(function (array $attributes) {
            $date = fake()->dateTimeBetween('-6 months', '-1 day');
            $hour = fake()->numberBetween(9, 16);
            $startTime = Carbon::instance($date)->setHour($hour)->setMinute(0)->setSecond(0);
            $endTime = (clone $startTime)->addMinutes(45);

            return [
                'appointment_date' => $startTime->toDateString(),
                'start_time' => $startTime,
                'end_time' => $endTime,
                'status' => 'completed',
            ];
        });
    }

    /**
     * Configure the factory to generate upcoming appointments.
     */
    public function upcoming(): Factory
    {
        return $this->state(function (array $attributes) {
            $date = fake()->dateTimeBetween('+1 day', '+30 days');
            $hour = fake()->numberBetween(9, 16);
            $startTime = Carbon::instance($date)->setHour($hour)->setMinute(0)->setSecond(0);
            $endTime = (clone $startTime)->addMinutes(45);

            return [
                'appointment_date' => $startTime->toDateString(),
                'start_time' => $startTime,
                'end_time' => $endTime,
                'status' => 'scheduled',
            ];
        });
    }
}
