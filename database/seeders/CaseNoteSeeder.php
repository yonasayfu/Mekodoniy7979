<?php

namespace Database\Seeders;

use App\Models\CaseNote;
use Illuminate\Database\Seeder;

class CaseNoteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        CaseNote::factory(50)->create();
    }
}
