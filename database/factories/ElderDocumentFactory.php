<?php

namespace Database\Factories;

use App\Models\Elder;
use App\Models\ElderDocument;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ElderDocument>
 */
class ElderDocumentFactory extends Factory
{
    protected $model = ElderDocument::class;

    public function definition(): array
    {
        return [
            'elder_id' => Elder::factory(),
            'type' => 'consent',
            'label' => 'Consent Form',
            'file_path' => 'elders/documents/'.$this->faker->uuid.'.pdf',
            'file_name' => $this->faker->lexify('consent_????.pdf'),
            'mime_type' => 'application/pdf',
            'uploaded_by' => User::factory(),
            'uploaded_at' => now()->subDays($this->faker->numberBetween(1, 90)),
        ];
    }
}
