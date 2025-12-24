<?php

namespace Database\Seeders;

use App\Models\ElderHealthAssessment;
use Illuminate\Database\Seeder;

class ElderHealthAssessmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        ElderHealthAssessment::factory(50)->create();
    }
}
