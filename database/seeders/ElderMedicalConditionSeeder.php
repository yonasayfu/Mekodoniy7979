<?php

namespace Database\Seeders;

use App\Models\ElderMedicalCondition;
use Illuminate\Database\Seeder;

class ElderMedicalConditionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        ElderMedicalCondition::factory(50)->create();
    }
}
