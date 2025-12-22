<?php

namespace Database\Seeders;

use App\Models\Branch;
use App\Models\Elder;
use App\Models\User;
use App\Models\Sponsorship;
use App\Models\Donation;
use App\Models\Visit;
use App\Models\TimelineEvent;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;

class MekodoniaSeeder extends Seeder
{
    /**
     * Run the database seeds for Mekodonia charity operations.
     */
    public function run(): void
    {
        $this->createEthiopianBranches();
        $this->createEthiopianElders();
        $this->createInternationalDonors();
        $this->createLocalEthiopianDonors();
        $this->createRelationshipBasedPledges();
        $this->createDonationHistory();
        $this->createVisitsAndFollowups();
        $this->createSuccessStories();
        $this->createTimelineActivities();
        $this->createSampleStaffUsers();
    }

    /**
     * Create Ethiopian branches in major cities.
     */
    private function createEthiopianBranches(): void
    {
        $branches = [
            [
                'name' => 'Addis Ababa Central',
                'location' => 'Bole Medhanealem, Addis Ababa',
                'contact_person' => 'Abebe Tesfaye',
                'contact_phone' => '+251 11 123 4567',
                'contact_email' => 'addis@mekodonia.org',
                'notes' => 'Main branch serving central Addis Ababa region with largest elder population',
                'is_active' => true,
            ],
            [
                'name' => 'Hawassa Branch',
                'location' => 'Hawassa City Center, Sidama Region',
                'contact_person' => 'Tigist Mengistu',
                'contact_phone' => '+251 46 220 1234',
                'contact_email' => 'hawassa@mekodonia.org',
                'notes' => 'Serving southern Ethiopia with focus on Sidama communities and rural elders',
                'is_active' => true,
            ],
            [
                'name' => 'Bahir Dar Branch',
                'location' => 'Near Lake Tana, Amhara Region',
                'contact_person' => 'Dawit Kebede',
                'contact_phone' => '+251 58 220 5678',
                'contact_email' => 'bahirdar@mekodonia.org',
                'notes' => 'Lake Tana region operations and Amhara community support',
                'is_active' => true,
            ],
            [
                'name' => 'Mekelle Branch',
                'location' => 'Mekelle City Center, Tigray Region',
                'contact_person' => 'Hirut Gebremariam',
                'contact_phone' => '+251 34 440 9012',
                'contact_email' => 'mekelle@mekodonia.org',
                'notes' => 'Northern Ethiopia operations with Tigrinya-speaking communities',
                'is_active' => true,
            ],
            [
                'name' => 'Dire Dawa Branch',
                'location' => 'Dire Dawa City Center, Eastern Ethiopia',
                'contact_person' => 'Ahmed Hassan',
                'contact_phone' => '+251 25 111 3456',
                'contact_email' => 'diredawa@mekodonia.org',
                'notes' => 'Eastern corridor operations and Somali-speaking communities',
                'is_active' => true,
            ],
            [
                'name' => 'Gondar Branch',
                'location' => 'Gondar City, Amhara Region',
                'contact_person' => 'Meseret Alemu',
                'contact_phone' => '+251 58 111 7890',
                'contact_email' => 'gondar@mekodonia.org',
                'notes' => 'Historic city operations with focus on university community support',
                'is_active' => true,
            ],
            [
                'name' => 'Jimma Branch',
                'location' => 'Jimma City, Oromia Region',
                'contact_person' => 'Kemal Ahmed',
                'contact_phone' => '+251 47 111 2345',
                'contact_email' => 'jimma@mekodonia.org',
                'notes' => 'Western Ethiopia operations with coffee-growing community focus',
                'is_active' => true,
            ],
        ];

        foreach ($branches as $branchData) {
            Branch::create($branchData);
        }
    }

    /**
     * Create elders with Ethiopian names and realistic backgrounds.
     */
    private function createEthiopianElders(): void
    {
        $branches = Branch::all();
        $elders = [
            // Addis Ababa elders
            [
                'branch_id' => $branches->where('name', 'Addis Ababa Central')->first()->id,
                'first_name' => 'Abebe',
                'last_name' => 'Tesfaye',
                'date_of_birth' => Carbon::now()->subYears(72),
                'gender' => 'male',
                'address' => 'Kazanchis, Addis Ababa',
                'city' => 'Addis Ababa',
                'country' => 'Ethiopia',
                'phone' => '+251 911 123 456',
                'bio' => 'Abebe was a farmer in rural Ethiopia before moving to Addis Ababa. He lost his wife 5 years ago and struggles with diabetes. He has three children who live abroad.',
                'priority_level' => 'high',
                'health_status' => 'fair',
                'special_needs' => 'Regular medical checkups for diabetes, monthly medication assistance',
                'monthly_expenses' => 2200,
                'relationship_type' => 'father',
                'relationship_priority' => 1,
                'is_featured' => true,
                'monthly_expenses_breakdown' => json_encode([
                    'food' => 800,
                    'medical' => 600,
                    'housing' => 500,
                    'utilities' => 200,
                    'transportation' => 100,
                ]),
            ],
            [
                'branch_id' => $branches->where('name', 'Addis Ababa Central')->first()->id,
                'first_name' => 'Tigist',
                'last_name' => 'Mengistu',
                'date_of_birth' => Carbon::now()->subYears(68),
                'gender' => 'female',
                'address' => 'Piassa, Addis Ababa',
                'city' => 'Addis Ababa',
                'country' => 'Ethiopia',
                'phone' => '+251 922 234 567',
                'bio' => 'Tigist raised 8 children as a single mother. She worked as a weaver in traditional Ethiopian textiles. Now she has arthritis and finds it difficult to continue her craft.',
                'priority_level' => 'high',
                'health_status' => 'poor',
                'special_needs' => 'Arthritis treatment, mobility assistance, traditional medicine supplies',
                'monthly_expenses' => 2100,
                'relationship_type' => 'mother',
                'relationship_priority' => 1,
                'is_featured' => true,
                'monthly_expenses_breakdown' => json_encode([
                    'food' => 750,
                    'medical' => 700,
                    'housing' => 450,
                    'utilities' => 150,
                    'transportation' => 50,
                ]),
            ],
            [
                'branch_id' => $branches->where('name', 'Addis Ababa Central')->first()->id,
                'first_name' => 'Dawit',
                'last_name' => 'Kebede',
                'date_of_birth' => Carbon::now()->subYears(75),
                'gender' => 'male',
                'address' => 'Merkato, Addis Ababa',
                'city' => 'Addis Ababa',
                'country' => 'Ethiopia',
                'phone' => '+251 933 345 678',
                'bio' => 'Dawit was a merchant in Merkato market for 40 years. He has heart problems and his children are sponsoring him through Mekodonia.',
                'priority_level' => 'medium',
                'health_status' => 'fair',
                'special_needs' => 'Heart medication, regular check-ups',
                'monthly_expenses' => 1900,
                'relationship_type' => 'father',
                'relationship_priority' => 2,
                'is_featured' => false,
                'monthly_expenses_breakdown' => json_encode([
                    'food' => 700,
                    'medical' => 500,
                    'housing' => 400,
                    'utilities' => 200,
                    'transportation' => 100,
                ]),
            ],
            // Hawassa elders
            [
                'branch_id' => $branches->where('name', 'Hawassa Branch')->first()->id,
                'first_name' => 'Meseret',
                'last_name' => 'Alemu',
                'date_of_birth' => Carbon::now()->subYears(69),
                'gender' => 'female',
                'address' => 'Hawassa City Center',
                'city' => 'Hawassa',
                'country' => 'Ethiopia',
                'phone' => '+251 946 456 789',
                'bio' => 'Meseret was a teacher in rural Sidama schools. She has grandchildren who sponsor her through international donors.',
                'priority_level' => 'medium',
                'health_status' => 'good',
                'special_needs' => 'Vision care, reading glasses',
                'monthly_expenses' => 1800,
                'relationship_type' => 'mother',
                'relationship_priority' => 2,
                'is_featured' => true,
                'monthly_expenses_breakdown' => json_encode([
                    'food' => 650,
                    'medical' => 400,
                    'housing' => 450,
                    'utilities' => 200,
                    'transportation' => 100,
                ]),
            ],
            [
                'branch_id' => $branches->where('name', 'Hawassa Branch')->first()->id,
                'first_name' => 'Kemal',
                'last_name' => 'Ahmed',
                'date_of_birth' => Carbon::now()->subYears(71),
                'gender' => 'male',
                'address' => 'Hawassa Industrial Zone',
                'city' => 'Hawassa',
                'country' => 'Ethiopia',
                'phone' => '+251 957 567 890',
                'bio' => 'Kemal worked in the Hawassa industrial park. He has respiratory issues from years of factory work.',
                'priority_level' => 'high',
                'health_status' => 'poor',
                'special_needs' => 'Respiratory treatment, oxygen support when needed',
                'monthly_expenses' => 2300,
                'relationship_type' => 'father',
                'relationship_priority' => 1,
                'is_featured' => false,
                'monthly_expenses_breakdown' => json_encode([
                    'food' => 800,
                    'medical' => 800,
                    'housing' => 400,
                    'utilities' => 200,
                    'transportation' => 100,
                ]),
            ],
            // Bahir Dar elders
            [
                'branch_id' => $branches->where('name', 'Bahir Dar Branch')->first()->id,
                'first_name' => 'Hirut',
                'last_name' => 'Gebremariam',
                'date_of_birth' => Carbon::now()->subYears(73),
                'gender' => 'female',
                'address' => 'Near Lake Tana, Bahir Dar',
                'city' => 'Bahir Dar',
                'country' => 'Ethiopia',
                'phone' => '+251 958 678 901',
                'bio' => 'Hirut was a fisherwoman on Lake Tana. She has 12 children and many grandchildren. Her family sponsors her through Mekodonia.',
                'priority_level' => 'medium',
                'health_status' => 'fair',
                'special_needs' => 'Joint pain treatment, fishing equipment assistance',
                'monthly_expenses' => 1950,
                'relationship_type' => 'mother',
                'relationship_priority' => 2,
                'is_featured' => true,
                'monthly_expenses_breakdown' => json_encode([
                    'food' => 700,
                    'medical' => 450,
                    'housing' => 500,
                    'utilities' => 200,
                    'transportation' => 100,
                ]),
            ],
            // Mekelle elders
            [
                'branch_id' => $branches->where('name', 'Mekelle Branch')->first()->id,
                'first_name' => 'Gebre',
                'last_name' => 'Michael',
                'date_of_birth' => Carbon::now()->subYears(76),
                'gender' => 'male',
                'address' => 'Mekelle Old Town',
                'city' => 'Mekelle',
                'country' => 'Ethiopia',
                'phone' => '+251 934 789 012',
                'bio' => 'Gebre was a priest in the Ethiopian Orthodox Church. He speaks Tigrinya and has served his community for 50 years.',
                'priority_level' => 'high',
                'health_status' => 'poor',
                'special_needs' => 'Church-related medical care, traditional ceremonies support',
                'monthly_expenses' => 2400,
                'relationship_type' => 'father',
                'relationship_priority' => 1,
                'is_featured' => true,
                'monthly_expenses_breakdown' => json_encode([
                    'food' => 850,
                    'medical' => 700,
                    'housing' => 550,
                    'utilities' => 200,
                    'transportation' => 100,
                ]),
            ],
            // Dire Dawa elders
            [
                'branch_id' => $branches->where('name', 'Dire Dawa Branch')->first()->id,
                'first_name' => 'Fatima',
                'last_name' => 'Omar',
                'date_of_birth' => Carbon::now()->subYears(70),
                'gender' => 'female',
                'address' => 'Dire Dawa Central',
                'city' => 'Dire Dawa',
                'country' => 'Ethiopia',
                'phone' => '+251 925 890 123',
                'bio' => 'Fatima is from Somali-speaking community in Dire Dawa. She raised 6 children and now receives support from her grandchildren abroad.',
                'priority_level' => 'medium',
                'health_status' => 'fair',
                'special_needs' => 'Cultural food assistance, Somali language support',
                'monthly_expenses' => 1850,
                'relationship_type' => 'mother',
                'relationship_priority' => 2,
                'is_featured' => false,
                'monthly_expenses_breakdown' => json_encode([
                    'food' => 680,
                    'medical' => 420,
                    'housing' => 480,
                    'utilities' => 180,
                    'transportation' => 90,
                ]),
            ],
            // Gondar elders
            [
                'branch_id' => $branches->where('name', 'Gondar Branch')->first()->id,
                'first_name' => 'Tekle',
                'last_name' => 'Hailemariam',
                'date_of_birth' => Carbon::now()->subYears(74),
                'gender' => 'male',
                'address' => 'Near Gondar University',
                'city' => 'Gondar',
                'country' => 'Ethiopia',
                'phone' => '+251 958 901 234',
                'bio' => 'Tekle was a professor at Gondar University. He has published several books on Ethiopian history and now needs support for his medical care.',
                'priority_level' => 'high',
                'health_status' => 'poor',
                'special_needs' => 'Specialized medical care, book publication support',
                'monthly_expenses' => 2500,
                'relationship_type' => 'father',
                'relationship_priority' => 1,
                'is_featured' => true,
                'monthly_expenses_breakdown' => json_encode([
                    'food' => 900,
                    'medical' => 800,
                    'housing' => 500,
                    'utilities' => 200,
                    'transportation' => 100,
                ]),
            ],
            // Jimma elders
            [
                'branch_id' => $branches->where('name', 'Jimma Branch')->first()->id,
                'first_name' => 'Worknesh',
                'last_name' => 'Desta',
                'date_of_birth' => Carbon::now()->subYears(67),
                'gender' => 'female',
                'address' => 'Jimma Coffee Region',
                'city' => 'Jimma',
                'country' => 'Ethiopia',
                'phone' => '+251 947 012 345',
                'bio' => 'Worknesh worked on coffee plantations in Jimma. She has 9 children and her family sponsors her through Mekodonia\'s relationship program.',
                'priority_level' => 'medium',
                'health_status' => 'good',
                'special_needs' => 'Coffee-related health monitoring, rural transportation',
                'monthly_expenses' => 1750,
                'relationship_type' => 'mother',
                'relationship_priority' => 2,
                'is_featured' => false,
                'monthly_expenses_breakdown' => json_encode([
                    'food' => 620,
                    'medical' => 380,
                    'housing' => 450,
                    'utilities' => 200,
                    'transportation' => 100,
                ]),
            ],
            // Additional elders for more variety
            [
                'branch_id' => $branches->where('name', 'Addis Ababa Central')->first()->id,
                'first_name' => 'Solomon',
                'last_name' => 'Getachew',
                'date_of_birth' => Carbon::now()->subYears(69),
                'gender' => 'male',
                'address' => 'Entoto Hill, Addis Ababa',
                'city' => 'Addis Ababa',
                'country' => 'Ethiopia',
                'phone' => '+251 911 345 678',
                'bio' => 'Solomon was a musician playing traditional Ethiopian instruments. His brother sponsors him through Mekodonia.',
                'priority_level' => 'medium',
                'health_status' => 'fair',
                'special_needs' => 'Musical instrument maintenance, performance opportunities',
                'monthly_expenses' => 2000,
                'relationship_type' => 'brother',
                'relationship_priority' => 3,
                'is_featured' => false,
                'monthly_expenses_breakdown' => json_encode([
                    'food' => 750,
                    'medical' => 450,
                    'housing' => 500,
                    'utilities' => 200,
                    'transportation' => 100,
                ]),
            ],
            [
                'branch_id' => $branches->where('name', 'Hawassa Branch')->first()->id,
                'first_name' => 'Aster',
                'last_name' => 'Tadesse',
                'date_of_birth' => Carbon::now()->subYears(65),
                'gender' => 'female',
                'address' => 'Hawassa Lake View',
                'city' => 'Hawassa',
                'country' => 'Ethiopia',
                'phone' => '+251 946 567 890',
                'bio' => 'Aster was a midwife in rural communities. Her daughter sponsors her through the international program.',
                'priority_level' => 'medium',
                'health_status' => 'good',
                'special_needs' => 'Midwifery supplies, community health education',
                'monthly_expenses' => 1900,
                'relationship_type' => 'mother',
                'relationship_priority' => 1,
                'is_featured' => true,
                'monthly_expenses_breakdown' => json_encode([
                    'food' => 700,
                    'medical' => 400,
                    'housing' => 500,
                    'utilities' => 200,
                    'transportation' => 100,
                ]),
            ],
            [
                'branch_id' => $branches->where('name', 'Mekelle Branch')->first()->id,
                'first_name' => 'Mussie',
                'last_name' => 'Tekle',
                'date_of_birth' => Carbon::now()->subYears(71),
                'gender' => 'male',
                'address' => 'Mekelle University Area',
                'city' => 'Mekelle',
                'country' => 'Ethiopia',
                'phone' => '+251 934 678 901',
                'bio' => 'Mussie was a farmer and community leader. His son in the diaspora sponsors him through Mekodonia.',
                'priority_level' => 'high',
                'health_status' => 'fair',
                'special_needs' => 'Agricultural support, community leadership training',
                'monthly_expenses' => 2100,
                'relationship_type' => 'father',
                'relationship_priority' => 1,
                'is_featured' => false,
                'monthly_expenses_breakdown' => json_encode([
                    'food' => 780,
                    'medical' => 520,
                    'housing' => 500,
                    'utilities' => 200,
                    'transportation' => 100,
                ]),
            ],
            [
                'branch_id' => $branches->where('name', 'Bahir Dar Branch')->first()->id,
                'first_name' => 'Almaz',
                'last_name' => 'Wondimu',
                'date_of_birth' => Carbon::now()->subYears(66),
                'gender' => 'female',
                'address' => 'Bahir Dar Peninsula',
                'city' => 'Bahir Dar',
                'country' => 'Ethiopia',
                'phone' => '+251 958 789 012',
                'bio' => 'Almaz was a traditional healer using Ethiopian medicinal plants. Her sister sponsors her through Mekodonia.',
                'priority_level' => 'medium',
                'health_status' => 'good',
                'special_needs' => 'Medicinal plant supplies, traditional healing practice support',
                'monthly_expenses' => 1850,
                'relationship_type' => 'sister',
                'relationship_priority' => 3,
                'is_featured' => false,
                'monthly_expenses_breakdown' => json_encode([
                    'food' => 680,
                    'medical' => 420,
                    'housing' => 480,
                    'utilities' => 180,
                    'transportation' => 90,
                ]),
            ],
        ];

        foreach ($elders as $elderData) {
            Elder::create($elderData);
        }
    }

    /**
     * Create international donors from various countries.
     */
    private function createInternationalDonors(): void
    {
        $branches = Branch::all();
        $internationalDonors = [
            // USA donors
            [
                'name' => 'Sarah Johnson',
                'email' => 'sarah.johnson@email.com',
                'country' => 'United States',
                'city' => 'Seattle',
                'phone' => '+1 206 555 0123',
                'relationship_to_elder' => 'daughter',
                'branch_id' => $branches->random()->id,
            ],
            [
                'name' => 'Michael Chen',
                'email' => 'michael.chen@email.com',
                'country' => 'United States',
                'city' => 'San Francisco',
                'phone' => '+1 415 555 0456',
                'relationship_to_elder' => 'son',
                'branch_id' => $branches->random()->id,
            ],
            [
                'name' => 'Emily Rodriguez',
                'email' => 'emily.rodriguez@email.com',
                'country' => 'United States',
                'city' => 'Austin',
                'phone' => '+1 512 555 0789',
                'relationship_to_elder' => 'granddaughter',
                'branch_id' => $branches->random()->id,
            ],
            // Canada donors
            [
                'name' => 'David Thompson',
                'email' => 'david.thompson@email.com',
                'country' => 'Canada',
                'city' => 'Toronto',
                'phone' => '+1 416 555 0321',
                'relationship_to_elder' => 'son',
                'branch_id' => $branches->random()->id,
            ],
            [
                'name' => 'Jennifer Liu',
                'email' => 'jennifer.liu@email.com',
                'country' => 'Canada',
                'city' => 'Vancouver',
                'phone' => '+1 604 555 0654',
                'relationship_to_elder' => 'daughter',
                'branch_id' => $branches->random()->id,
            ],
            // UK donors
            [
                'name' => 'James Wilson',
                'email' => 'james.wilson@email.com',
                'country' => 'United Kingdom',
                'city' => 'London',
                'phone' => '+44 20 555 0987',
                'relationship_to_elder' => 'grandson',
                'branch_id' => $branches->random()->id,
            ],
            [
                'name' => 'Rachel Green',
                'email' => 'rachel.green@email.com',
                'country' => 'United Kingdom',
                'city' => 'Manchester',
                'phone' => '+44 161 555 0432',
                'relationship_to_elder' => 'granddaughter',
                'branch_id' => $branches->random()->id,
            ],
            // Spain donors
            [
                'name' => 'Carlos Martinez',
                'email' => 'carlos.martinez@email.com',
                'country' => 'Spain',
                'city' => 'Barcelona',
                'phone' => '+34 93 555 0765',
                'relationship_to_elder' => 'son',
                'branch_id' => $branches->random()->id,
            ],
            // Germany donors
            [
                'name' => 'Anna Schmidt',
                'email' => 'anna.schmidt@email.com',
                'country' => 'Germany',
                'city' => 'Berlin',
                'phone' => '+49 30 555 0213',
                'relationship_to_elder' => 'daughter',
                'branch_id' => $branches->random()->id,
            ],
            [
                'name' => 'Thomas Mueller',
                'email' => 'thomas.mueller@email.com',
                'country' => 'Germany',
                'city' => 'Munich',
                'phone' => '+49 89 555 0543',
                'relationship_to_elder' => 'son',
                'branch_id' => $branches->random()->id,
            ],
            // Additional international donors
            [
                'name' => 'Maria Garcia',
                'email' => 'maria.garcia@email.com',
                'country' => 'Spain',
                'city' => 'Madrid',
                'phone' => '+34 91 555 0876',
                'relationship_to_elder' => 'niece',
                'branch_id' => $branches->random()->id,
            ],
            [
                'name' => 'Pierre Dubois',
                'email' => 'pierre.dubois@email.com',
                'country' => 'France',
                'city' => 'Paris',
                'phone' => '+33 1 555 0321',
                'relationship_to_elder' => 'nephew',
                'branch_id' => $branches->random()->id,
            ],
            [
                'name' => 'Lisa Anderson',
                'email' => 'lisa.anderson@email.com',
                'country' => 'Sweden',
                'city' => 'Stockholm',
                'phone' => '+46 8 555 0654',
                'relationship_to_elder' => 'granddaughter',
                'branch_id' => $branches->random()->id,
            ],
            [
                'name' => 'Ahmed Hassan',
                'email' => 'ahmed.hassan@email.com',
                'country' => 'United Arab Emirates',
                'city' => 'Dubai',
                'phone' => '+971 4 555 0987',
                'relationship_to_elder' => 'son',
                'branch_id' => $branches->random()->id,
            ],
            [
                'name' => 'Yuki Tanaka',
                'email' => 'yuki.tanaka@email.com',
                'country' => 'Japan',
                'city' => 'Tokyo',
                'phone' => '+81 3 555 0432',
                'relationship_to_elder' => 'daughter',
                'branch_id' => $branches->random()->id,
            ],
        ];

        foreach ($internationalDonors as $donorData) {
            $user = User::create([
                'name' => $donorData['name'],
                'email' => $donorData['email'],
                'password' => Hash::make('password'),
                'account_status' => User::STATUS_ACTIVE,
                'account_type' => User::TYPE_EXTERNAL,
                'approved_at' => Carbon::now(),
                'approved_by' => 1,
                'branch_id' => $donorData['branch_id'],
            ]);

            $user->assignRole('External');
        }
    }

    /**
     * Create local Ethiopian donors.
     */
    private function createLocalEthiopianDonors(): void
    {
        $branches = Branch::all();
        $localDonors = [
            [
                'name' => 'Eyob Mekonnen',
                'email' => 'eyob.mekonnen@email.com',
                'city' => 'Addis Ababa',
                'phone' => '+251 911 234 567',
                'relationship_to_elder' => 'brother',
                'branch_id' => $branches->where('name', 'Addis Ababa Central')->first()->id,
            ],
            [
                'name' => 'Helen Teshome',
                'email' => 'helen.teshome@email.com',
                'city' => 'Addis Ababa',
                'phone' => '+251 922 345 678',
                'relationship_to_elder' => 'sister',
                'branch_id' => $branches->where('name', 'Addis Ababa Central')->first()->id,
            ],
            [
                'name' => 'Daniel Asfaw',
                'email' => 'daniel.asfaw@email.com',
                'city' => 'Hawassa',
                'phone' => '+251 946 456 789',
                'relationship_to_elder' => 'son',
                'branch_id' => $branches->where('name', 'Hawassa Branch')->first()->id,
            ],
            [
                'name' => 'Martha Gebremichael',
                'email' => 'martha.gebremichael@email.com',
                'city' => 'Mekelle',
                'phone' => '+251 934 567 890',
                'relationship_to_elder' => 'daughter',
                'branch_id' => $branches->where('name', 'Mekelle Branch')->first()->id,
            ],
            [
                'name' => 'Samuel Tadesse',
                'email' => 'samuel.tadesse@email.com',
                'city' => 'Bahir Dar',
                'phone' => '+251 958 678 901',
                'relationship_to_elder' => 'grandson',
                'branch_id' => $branches->where('name', 'Bahir Dar Branch')->first()->id,
            ],
            [
                'name' => 'Amina Ibrahim',
                'email' => 'amina.ibrahim@email.com',
                'city' => 'Dire Dawa',
                'phone' => '+251 925 789 012',
                'relationship_to_elder' => 'granddaughter',
                'branch_id' => $branches->where('name', 'Dire Dawa Branch')->first()->id,
            ],
            [
                'name' => 'Joseph Hailemariam',
                'email' => 'joseph.hailemariam@email.com',
                'city' => 'Gondar',
                'phone' => '+251 958 890 123',
                'relationship_to_elder' => 'nephew',
                'branch_id' => $branches->where('name', 'Gondar Branch')->first()->id,
            ],
            [
                'name' => 'Rebecca Desta',
                'email' => 'rebecca.desta@email.com',
                'city' => 'Jimma',
                'phone' => '+251 947 901 234',
                'relationship_to_elder' => 'niece',
                'branch_id' => $branches->where('name', 'Jimma Branch')->first()->id,
            ],
            [
                'name' => 'Abraham Kebede',
                'email' => 'abraham.kebede@email.com',
                'city' => 'Addis Ababa',
                'phone' => '+251 911 012 345',
                'relationship_to_elder' => 'cousin',
                'branch_id' => $branches->where('name', 'Addis Ababa Central')->first()->id,
            ],
            [
                'name' => 'Selamawit Mengistu',
                'email' => 'selamawit.mengistu@email.com',
                'city' => 'Hawassa',
                'phone' => '+251 946 123 456',
                'relationship_to_elder' => 'cousin',
                'branch_id' => $branches->where('name', 'Hawassa Branch')->first()->id,
            ],
        ];

        foreach ($localDonors as $donorData) {
            $user = User::create([
                'name' => $donorData['name'],
                'email' => $donorData['email'],
                'password' => Hash::make('password'),
                'account_status' => User::STATUS_ACTIVE,
                'account_type' => User::TYPE_EXTERNAL,
                'approved_at' => Carbon::now(),
                'approved_by' => 1,
                'branch_id' => $donorData['branch_id'],
            ]);

            $user->assignRole('External');
        }
    }

    /**
     * Create relationship-based sponsorships matching donors with elders.
     */
    private function createRelationshipBasedPledges(): void
    {
        $elders = Elder::all();
                $users = User::where('account_type', User::TYPE_EXTERNAL)->get();
        $relationshipTypes = ['father', 'mother', 'brother', 'sister'];

        $sponsorships = [];

        // Create sponsorships based on relationship matching
        foreach ($elders as $elder) {
            $matchingUsers = $users->filter(function ($user) use ($elder) {
                // Try to match users who might be related to this elder
                // In a real system, this would be more sophisticated
                return rand(1, 3) === 1; // 33% chance of matching
            });

            $sponsorshipCount = rand(1, 3); // 1-3 sponsorships per elder
            $selectedUsers = $matchingUsers->take($sponsorshipCount);

            if ($selectedUsers->isEmpty()) {
                // If no matching users, assign random users
                $selectedUsers = $users->random(min($sponsorshipCount, $users->count()));
            }

                        foreach ($selectedUsers as $user) {
                $relationshipType = $relationshipTypes[array_rand($relationshipTypes)];
                $sponsorships[] = [
                    'user_id' => $user->id,
                    'elder_id' => $elder->id,
                    'amount' => rand(150, 300), // 150-300 ETB per month
                    'currency' => 'ETB',
                    'relationship_type' => $relationshipType,
                    'start_date' => Carbon::now()->subMonths(rand(1, 12)),
                    'status' => (rand(1, 10) <= 9) ? 'active' : 'inactive', // 90% active
                    'frequency' => 'monthly',
                    'notes' => 'Supporting ' . $relationshipType . ' through Mekodonia relationship program',
                ];
            }
        }

        foreach ($sponsorships as $sponsorshipData) {
            Sponsorship::create($sponsorshipData);
        }
    }

    /**
     * Create donation history for active sponsorships.
     */
    private function createDonationHistory(): void
    {
        $sponsorships = Sponsorship::where('status', 'active')->get();

        foreach ($sponsorships as $sponsorship) {
            $startDate = Carbon::parse($sponsorship->start_date);
            $monthsActive = min($startDate->diffInMonths(Carbon::now()), $sponsorship->consecutive_months_kept);
            $donationCount = max(1, $monthsActive); // At least 1 donation

            for ($i = 0; $i < $donationCount; $i++) {
                Donation::create([
                    'user_id' => $sponsorship->user_id,
                    'sponsorship_id' => $sponsorship->id,
                    'elder_id' => $sponsorship->elder_id,
                    'amount' => $sponsorship->amount,
                    'currency' => $sponsorship->currency,
                    'payment_gateway' => 'stripe',
                    'status' => 'approved',
                    'donation_type' => 'pledge',
                    'created_at' => $startDate->copy()->addMonths($i),
                ]);
            }
        }
    }

    /**
     * Create visits and follow-ups for elders.
     */
    private function createVisitsAndFollowups(): void
    {
        $elders = Elder::all();
        $staffUsers = User::where('account_type', User::TYPE_INTERNAL)->get();

        // If no internal staff users exist yet, use any available users
        if ($staffUsers->isEmpty()) {
            $staffUsers = User::all();
        }

        if ($staffUsers->isEmpty()) {
            return; // Skip if no users exist
        }

        foreach ($elders as $elder) {
            // Create 2-4 visits per elder
            $visitCount = rand(2, 4);

            for ($i = 0; $i < $visitCount; $i++) {
                Visit::create([
                    'user_id' => $staffUsers->random()->id,
                    'elder_id' => $elder->id,
                    'branch_id' => $elder->branch_id,
                    'visit_date' => Carbon::now()->subDays(rand(1, 90)),
                    'purpose' => collect(['Home visit', 'Medical checkup', 'General followup', 'Emergency visit'])->random(),
                    'notes' => "Regular check-in with {$elder->first_name}. Discussed health conditions and provided support.",
                    'status' => 'completed',
                    'approved_by' => $staffUsers->first()->id,
                ]);
            }
        }
    }

    /**
     * Create success stories and timeline events.
     */
    private function createSuccessStories(): void
    {
        $featuredElders = Elder::where('is_featured', true)->get();
        $users = User::all();

        foreach ($featuredElders as $elder) {
            // Create timeline events for success stories
            $timelineEvents = [
                [
                    'type' => 'Pledge Started',
                    'description' => "Pledge started for {$elder->first_name} {$elder->last_name}",
                    'occurred_at' => Carbon::now()->subMonths(rand(6, 12)),
                ],
                [
                    'type' => 'Health Improvement',
                    'description' => "{$elder->first_name} showed significant health improvement after receiving regular medical support",
                    'occurred_at' => Carbon::now()->subMonths(rand(2, 6)),
                ],
                [
                    'type' => 'Community Recognition',
                    'description' => "{$elder->first_name} received community recognition for maintaining family traditions",
                    'occurred_at' => Carbon::now()->subMonths(rand(1, 3)),
                ],
                [
                    'type' => 'Family Reunion',
                    'description' => "Successful family reunion facilitated through Mekodonia's relationship program",
                    'occurred_at' => Carbon::now()->subDays(rand(1, 30)),
                ],
            ];

            foreach ($timelineEvents as $event) {
                TimelineEvent::create([
                    'user_id' => $users->random()->id,
                    'elder_id' => $elder->id,
                    'type' => $event['type'],
                    'description' => $event['description'],
                    'occurred_at' => $event['occurred_at'],
                ]);
            }
        }
    }

    /**
     * Create timeline activities for general engagement.
     */
    private function createTimelineActivities(): void
    {
        $elders = Elder::all();
        $users = User::all();

        // Create additional timeline events for all elders
        foreach ($elders->take(20) as $elder) { // Create events for 20 random elders
            $eventCount = rand(1, 3);

            for ($i = 0; $i < $eventCount; $i++) {
                $eventTypes = [
                    'Medical Checkup',
                    'Home Visit',
                    'Donation Received',
                    'Family Update',
                    'Community Event',
                    'Health Milestone',
                ];

                TimelineEvent::create([
                    'user_id' => $users->random()->id,
                    'elder_id' => $elder->id,
                    'type' => collect($eventTypes)->random(),
                    'description' => "Regular update for {$elder->first_name} {$elder->last_name}",
                    'occurred_at' => Carbon::now()->subDays(rand(1, 60)),
                ]);
            }
        }
    }

    /**
     * Create sample staff users for demonstration.
     */
    private function createSampleStaffUsers(): void
    {
        $branches = Branch::all();

        $staffUsers = [
            [
                'name' => 'Admin User',
                'email' => 'admin@mekodonia.org',
                'role' => 'Super Admin',
                'branch_id' => $branches->first()->id,
                'job_title' => 'System Administrator',
            ],
            [
                'name' => 'Branch Manager Addis',
                'email' => 'manager.addis@mekodonia.org',
                'role' => 'Manager',
                'branch_id' => $branches->where('name', 'Addis Ababa Central')->first()->id,
                'job_title' => 'Branch Manager',
            ],
            [
                'name' => 'Field Officer Hawassa',
                'email' => 'officer.hawassa@mekodonia.org',
                'role' => 'Field Officer',
                'branch_id' => $branches->where('name', 'Hawassa Branch')->first()->id,
                'job_title' => 'Field Officer',
            ],
            [
                'name' => 'Finance Officer',
                'email' => 'finance@mekodonia.org',
                'role' => 'Finance Officer',
                'branch_id' => $branches->first()->id,
                'job_title' => 'Finance Officer',
            ],
            [
                'name' => 'Reporting Analyst',
                'email' => 'reports@mekodonia.org',
                'role' => 'Reporting Analyst',
                'branch_id' => $branches->first()->id,
                'job_title' => 'Reporting Analyst',
            ],
        ];

        foreach ($staffUsers as $staffData) {
            $user = User::create([
                'name' => $staffData['name'],
                'email' => $staffData['email'],
                'password' => Hash::make('password'),
                'account_status' => User::STATUS_ACTIVE,
                'account_type' => User::TYPE_INTERNAL,
                'approved_at' => Carbon::now(),
                'approved_by' => 1,
                'branch_id' => $staffData['branch_id'],
            ]);

            $user->assignRole($staffData['role']);
        }
    }
}
