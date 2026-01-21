<?php

use App\Models\Donation;
use App\Models\Elder;
use App\Models\User;
use App\Models\Visit;
use App\Models\TimelineEvent;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\post;
use function Pest\Laravel\put;
use function Pest\Laravel\delete;

uses(RefreshDatabase::class);

it('records a guest donation and redirects home', function () {
    $elder = Elder::factory()->create();

    $payload = [
        'amount' => 150,
        'name' => 'Guest Donor',
        'email' => 'guest@example.com',
        'phone' => '+251900000000',
        'elder_id' => $elder->id,
        'notes' => 'Monthly breakfast sponsor',
    ];

    $response = post(route('donations.guest.store'), $payload);

    $response->assertRedirect(route('home'))
        ->assertSessionHas('success');

    $donation = Donation::first();

    expect($donation)
        ->not->toBeNull()
        ->branch_id->toEqual($elder->branch_id)
        ->amount->toEqual(150.0)
        ->guest_name->toEqual('Guest Donor')
        ->guest_email->toEqual('guest@example.com')
        ->notes->toEqual('Monthly breakfast sponsor');
});

it('allows staff to create and update sponsorship records', function () {
    $staff = User::factory()->create();
    $staff->givePermissionTo('sponsorships.manage');

    $donor = User::factory()->create();
    $elder = Elder::factory()->create();

    actingAs($staff);

    $storePayload = [
        'user_id' => $donor->id,
        'elder_id' => $elder->id,
        'amount' => 1000,
        'currency' => 'ETB',
        'frequency' => 'monthly',
        'relationship_type' => 'father',
        'start_date' => now()->toDateString(),
        'status' => 'active',
    ];

    $this->post(route('sponsorships.store'), $storePayload)
        ->assertRedirect(route('sponsorships.index'));

    $sponsorship = \App\Models\Sponsorship::first();

    expect($sponsorship)
        ->branch_id->toEqual($elder->branch_id)
        ->amount->toEqual(1000.0)
        ->relationship_type->toEqual('father');

    $createdEvent = TimelineEvent::where('type', 'sponsorship_created')->first();

    expect($createdEvent)
        ->not->toBeNull()
        ->elder_id->toEqual($elder->id)
        ->user_id->toEqual($donor->id);

    $updatePayload = [
        'user_id' => $donor->id,
        'elder_id' => $elder->id,
        'amount' => 1200,
        'currency' => 'ETB',
        'frequency' => 'monthly',
        'relationship_type' => 'father',
        'start_date' => now()->toDateString(),
        'status' => 'active',
    ];

    $this->put(route('sponsorships.update', $sponsorship), $updatePayload)
        ->assertRedirect(route('sponsorships.index'));

    expect($sponsorship->refresh()->amount)->toEqual(1200.0);

    $updatedEvent = TimelineEvent::where('type', 'sponsorship_updated')->first();

    expect($updatedEvent)
        ->not->toBeNull()
        ->elder_id->toEqual($elder->id)
        ->user_id->toEqual($donor->id);

    $this->delete(route('sponsorships.destroy', $sponsorship))
        ->assertRedirect(route('sponsorships.index'));

    expect(\App\Models\Sponsorship::count())->toBe(0);
});

it('lets branch staff request and approve visits within their branch scope', function () {
    $staff = User::factory()->create(['branch_id' => null]);
    $staff->givePermissionTo('visits.manage');

    $elder = Elder::factory()->create();

    actingAs($staff);

    $storePayload = [
        'elder_id' => $elder->id,
        'user_id' => $staff->id,
        'visit_date' => now()->addDay()->toDateString(),
        'purpose' => 'Wellness visit',
        'status' => 'pending',
    ];

    $this->post(route('visits.store'), $storePayload)
        ->assertRedirect(route('visits.index'));

    $visit = Visit::first();

    expect($visit)
        ->not->toBeNull()
        ->branch_id->toEqual($elder->branch_id)
        ->purpose->toEqual('Wellness visit');

    $updatePayload = [
        'elder_id' => $elder->id,
        'user_id' => $staff->id,
        'visit_date' => now()->addDay()->toDateString(),
        'purpose' => 'Wellness visit',
        'status' => 'approved',
    ];

    $this->put(route('visits.update', $visit), $updatePayload)
        ->assertRedirect(route('visits.index'));

    expect($visit->refresh()->status)->toEqual('approved');
});
