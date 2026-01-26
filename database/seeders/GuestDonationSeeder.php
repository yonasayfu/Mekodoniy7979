<?php

namespace Database\Seeders;

use App\Models\Donation;
use App\Models\Elder;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class GuestDonationSeeder extends Seeder
{
    public function run(): void
    {
        $templates = [
            [
                'guest_name' => 'Telebirr Sponsor Demo',
                'guest_email' => 'telebirr-demo@mekodonia.org',
                'guest_phone' => '+251900000111',
                'amount' => 2_200,
                'payment_gateway' => 'telebirr',
                'payment_reference' => 'MHC-PEND-TELE-001',
                'payment_status' => 'awaiting_receipt',
                'donation_type' => 'guest_sponsorship',
                'cadence' => 'monthly',
                'recurrence_duration' => 12,
                'deduction_schedule' => 'Deduct on the 1st of every month.',
                'notes' => 'Waiting for Telebirr screenshot before finance confirms.',
                'include_receipt' => true,
                'include_mandate' => false,
            ],
            [
                'guest_name' => 'Bank Mandate Demo',
                'guest_email' => 'bank-demo@mekodonia.org',
                'guest_phone' => '+251900000222',
                'amount' => 1_500,
                'payment_gateway' => 'bank',
                'payment_reference' => 'MHC-PEND-BANK-001',
                'payment_status' => 'awaiting_receipt',
                'donation_type' => 'guest_sponsorship',
                'cadence' => 'monthly',
                'recurrence_duration' => 6,
                'deduction_schedule' => 'Deduct for 6 months starting February.',
                'notes' => 'Mandate signed, waiting for the branch to upload the scan.',
                'include_receipt' => false,
                'include_mandate' => true,
            ],
            [
                'guest_name' => 'Meal Gift Demo',
                'guest_email' => 'meal-demo@mekodonia.org',
                'guest_phone' => '+251900000333',
                'amount' => 120,
                'payment_gateway' => 'telebirr',
                'payment_reference' => 'MHC-PEND-MEAL-001',
                'payment_status' => 'pending',
                'donation_type' => 'guest_meal',
                'cadence' => 'one_time',
                'recurrence_duration' => null,
                'deduction_schedule' => 'One-day meal, reconcile with the kitchen team.',
                'notes' => 'Meal donation awaiting finance confirmation.',
                'include_receipt' => true,
                'include_mandate' => false,
            ],
        ];

        $needed = count($templates);
        $elders = Elder::inRandomOrder()->take($needed)->get();
        if ($elders->count() < $needed) {
            $elders = $elders->concat(
                Elder::factory()
                    ->count($needed - $elders->count())
                    ->create(),
            );
        }
        $elders = $elders->values();

        foreach ($templates as $index => $template) {
            if (Donation::where('payment_reference', $template['payment_reference'])->exists()) {
                continue;
            }

            $elder = $elders[$index % $elders->count()];
            $slug = Str::slug($template['payment_reference']);
            $receiptPath = "receipts/demo/{$slug}_receipt.pdf";
            $mandatePath = "receipts/demo/{$slug}_mandate.pdf";

            if (($template['include_receipt'] ?? false)) {
                Storage::disk('public')->put(
                    $receiptPath,
                    sprintf('Demo receipt for %s', $template['payment_reference']),
                );
            } else {
                $receiptPath = null;
            }

            if (($template['include_mandate'] ?? false)) {
                Storage::disk('public')->put(
                    $mandatePath,
                    sprintf('Demo mandate for %s', $template['payment_reference']),
                );
            } else {
                $mandatePath = null;
            }

            Donation::create([
                'elder_id' => $elder->id,
                'branch_id' => $elder->branch_id,
                'amount' => $template['amount'],
                'currency' => 'ETB',
                'guest_name' => $template['guest_name'],
                'guest_email' => $template['guest_email'],
                'guest_phone' => $template['guest_phone'],
                'payment_gateway' => $template['payment_gateway'],
                'payment_reference' => $template['payment_reference'],
                'payment_status' => $template['payment_status'],
                'status' => 'pending',
                'donation_type' => $template['donation_type'],
                'notes' => $template['notes'],
                'cadence' => $template['cadence'],
                'recurrence_duration' => $template['recurrence_duration'],
                'deduction_schedule' => $template['deduction_schedule'],
                'receipt_path' => $receiptPath,
                'mandate_path' => $mandatePath,
                'kyc_required' => false,
                'kyc_status' => 'not_required',
            ]);
        }
    }
}
