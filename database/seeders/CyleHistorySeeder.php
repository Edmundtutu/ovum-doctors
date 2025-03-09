<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CyleHistorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \App\Models\Patient::all()->each(function ($patient) {
            \App\Models\CyleHistory::factory(3)->create([
                'patient_id' => $patient->id,
            ]);
        });
    }
}
