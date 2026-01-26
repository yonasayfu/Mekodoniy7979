<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Branch;
use App\Models\Donation;
use App\Models\Elder;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Mockery;
use Tests\TestCase;

class GuestPaymentFlowTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Notification::fake();
        Storage::fake('public');

        $pdf = Mockery::mock();
        $pdf->shouldReceive('output')->andReturn('pdf-bytes');
        Pdf::shouldReceive('loadView')->andReturn($pdf);
    }

    protected function tearDown(): void
    {
        Mockery::close();

        parent::tearDown();
    }

    public function test_guest_payment_flow_records_cadence_and_files(): void
    {
        $branch = Branch::factory()->create();
        $elder = Elder::factory()->create([
            'branch_id' => $branch->id,
            'relationship_type' => 'father',
        ]);

        $response = $this->post(route('donations.guest.store'), [
            'amount' => 750,
            'elder_id' => $elder->id,
            'donation_mode' => 'sponsorship',
            'relationship' => 'father',
            'payment_gateway' => 'bank',
            'cadence' => 'monthly',
            'recurrence_duration' => 12,
            'deduction_schedule' => 'Deduct for 12 months',
            'name' => 'Guest Tester',
            'email' => 'guest@example.com',
            'notes' => 'Testing cadence metadata',
            'receipt' => UploadedFile::fake()->image('receipt.png'),
            'mandate' => UploadedFile::fake()->create('mandate.pdf', 150),
        ]);

        $response->assertRedirect(route('thank-you'));

        $donation = Donation::latest()->firstOrFail();

        $this->assertSame('bank', $donation->payment_gateway);
        $this->assertSame('awaiting_receipt', $donation->payment_status);
        $this->assertSame('monthly', $donation->cadence);
        $this->assertSame(12, $donation->recurrence_duration);
        $this->assertSame('Deduct for 12 months', $donation->deduction_schedule);
        $this->assertNotNull($donation->receipt_path);
        $this->assertNotNull($donation->mandate_path);

        Storage::disk('public')->assertExists($donation->receipt_path);
        Storage::disk('public')->assertExists($donation->mandate_path);
    }
}
