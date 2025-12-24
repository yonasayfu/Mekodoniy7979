<?php

namespace Database\Seeders;

use App\Models\Elder;
use Illuminate\Database\Seeder;

class ElderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Elder::factory(20)->create();
    }
}
