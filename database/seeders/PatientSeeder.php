<?php

namespace Database\Seeders;

use App\Models\Doctor;
use App\Models\Patient;
use Illuminate\Database\Seeder;

class PatientSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get all doctors or create one if none exist
        $doctors = Doctor::all();
        
        if ($doctors->isEmpty()) {
            $doctors = Doctor::factory()->count(1)->create();
        }
        
        // Create 10 patients with '0000' passcode for each doctor
        foreach ($doctors as $doctor) {
            Patient::factory()
                ->count(10)
                ->withPasscode('0000')
                ->create([
                    'doctor_id' => $doctor->id
                ]);
        }
        
        // Create one special test patient with known details
        Patient::factory()
            ->withPasscode('0000')
            ->create([
                'doctor_id' => $doctors->first()->id,
                'name' => 'Test Patient',
                'email' => 'patient@example.com',
                'phone' => '1234567890'
            ]);
    }
}
