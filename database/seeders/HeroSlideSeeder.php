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
                'image' => '/images/hero-father.svg',
                'cta_text' => 'Find a Father',
                'cta_link' => 'modal:father',
                'order' => 1,
            ],
            [
                'title' => 'Become a Mother Today',
                'description' => 'Extend your family by supporting an elderly woman. Your sponsorship is a promise of care, comfort, and companionship.',
                'image' => '/images/hero-mother.svg',
                'cta_text' => 'Find a Mother',
                'cta_link' => 'modal:mother',
                'order' => 2,
            ],
            [
                'title' => 'Support a Brother or Sister',
                'description' => 'Help a younger resident in need. Your contribution can fund education, health, and a brighter future.',
                'image' => '/images/hero-brother.svg',
                'cta_text' => 'Support a Sibling',
                'cta_link' => 'modal:brother',
                'order' => 3,
            ],
            [
                'title' => 'Give the Gift of Family',
                'description' => 'A small, one-time donation can provide a festive meal, warm clothing, or essential medicine. Make an immediate impact.',
                'image' => '/images/hero-sister.svg',
                'cta_text' => 'Donate a Meal',
                'cta_link' => '/guest-donation', // Using relative path or named route in controller
                'order' => 4,
            ],
        ];

        foreach ($slides as $slide) {
            \App\Models\HeroSlide::updateOrCreate(
                ['title' => $slide['title']],
                $slide
            );
        }
    }
}
