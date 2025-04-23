<?php

namespace Database\Seeders;

use App\Models\Clinic;
use App\Models\Doctor;
use App\Models\Vitals;
use App\Models\Patient;
use App\Models\Appointment;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\CyleHistory;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create 2 clinics
        $clinics = Clinic::factory(2)->create();

        // Create exactly 2 doctors, one for each clinic
        $doctors = collect();
        foreach ($clinics as $clinic) {
            $doctors->push(
                Doctor::factory()->create([
                    'clinic_id' => $clinic->id,
                    'email' => $clinic->id === 1 ? 'doctor1@example.com' : 'doctor2@example.com',
                    'password' => bcrypt('password') // Same password for easy testing
                ])
            );
        }

        // Create exactly 5 patients, distributed between the doctors
        $patients = collect();
        for ($i = 0; $i < 5; $i++) {
            $doctor = $doctors->random(); // Randomly assign to one of the doctors
            $patients->push(
                Patient::factory()
                    ->withPasscode('0000') // Add standard test passcode
                    ->create([
                        'doctor_id' => $doctor->id,
                        'email' => "patient{$i}@example.com"
                    ])
            );
        }

        // Create one special test patient with known details
        $patients->push(
            Patient::factory()
                ->withPasscode('0000')
                ->create([
                    'doctor_id' => $doctors->first()->id,
                    'name' => 'Test Patient',
                    'email' => 'patient@example.com',
                    'phone' => '1234567890'
                ])
        );

        // Create vitals records for each patient
        foreach ($patients as $patient) {
            // Create 3 vitals records per patient
            Vitals::factory(3)->create([
                'patient_id' => $patient->id
            ]);
        }

        // Create appointments
        foreach ($patients as $patient) {
            // Create 2 past appointments
            Appointment::factory(2)
                ->past()
                ->create([
                    'doctor_id' => $patient->doctor_id,
                    'patient_id' => $patient->id
                ]);

            // Create 1 upcoming appointment
            Appointment::factory()
                ->upcoming()
                ->create([
                    'doctor_id' => $patient->doctor_id,
                    'patient_id' => $patient->id
                ]);
        }

        // create a 3 cycle Histories/Records for each patient
        foreach ($patients as $patient) {
            // Create 3 cycle histories/records for each patient
            CyleHistory::factory(3)->create([
                'patient_id' => $patient->id
            ]);
        }
    }
}
