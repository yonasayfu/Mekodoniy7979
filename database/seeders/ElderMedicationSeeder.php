<?php

namespace Database\Seeders;

use App\Models\ElderMedication;
use Illuminate\Database\Seeder;

class ElderMedicationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        ElderMedication::factory(100)->create();
    }
}
