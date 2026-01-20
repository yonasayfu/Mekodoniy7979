<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class HeroSlideSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $slides = [
            [
                'title' => 'Become a Father Today',
                'description' => 'Embrace an elder with the honor and respect of a father figure. Your support provides more than just essentials; it provides dignity.',
                'image' => 'https://via.placeholder.com/1200x400.png/3498db/ffffff?text=Photo+of+a+Dignified+Elderly+Man',
                'cta_text' => 'Find a Father',
                'cta_link' => '#elders-gallery',
                'order' => 1,
            ],
            [
                'title' => 'Become a Mother Today',
                'description' => 'Extend your family by supporting an elderly woman. Your sponsorship is a promise of care, comfort, and companionship.',
                'image' => 'https://via.placeholder.com/1200x400.png/e74c3c/ffffff?text=Photo+of+a+Kind+Elderly+Woman',
                'cta_text' => 'Find a Mother',
                'cta_link' => '#elders-gallery',
                'order' => 2,
            ],
            [
                'title' => 'Support a Brother or Sister',
                'description' => 'Help a younger resident in need. Your contribution can fund education, health, and a brighter future.',
                'image' => 'https://via.placeholder.com/1200x400.png/2ecc71/ffffff?text=Photo+of+a+Hopeful+Youth',
                'cta_text' => 'Support a Sibling',
                'cta_link' => '#elders-gallery',
                'order' => 3,
            ],
            [
                'title' => 'Give the Gift of Family',
                'description' => 'A small, one-time donation can provide a festive meal, warm clothing, or essential medicine. Make an immediate impact.',
                'image' => 'https://via.placeholder.com/1200x400.png/f1c40f/000000?text=Donate+for+Immediate+Needs',
                'cta_text' => 'Donate a Meal',
                'cta_link' => '/guest-donation', // Using relative path or named route in controller
                'order' => 4,
            ],
        ];

        foreach ($slides as $slide) {
            \App\Models\HeroSlide::create($slide);
        }
    }
}
