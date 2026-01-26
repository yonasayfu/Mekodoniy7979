<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreDonationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'amount' => ['required', 'numeric'],
            'elder_id' => ['nullable', 'integer', 'exists:elders,id'],
            'campaign_id' => ['nullable', 'integer', 'exists:campaigns,id'],
            'relationship' => ['nullable', 'string'],
            'cadence' => ['nullable', 'string', 'in:one_time,weekly,monthly,quarterly,annual,custom'],
            'recurrence_duration' => ['nullable', 'integer', 'min:1'],
            'deduction_schedule' => ['nullable', 'string', 'max:255'],
            'payment_gateway' => ['nullable', 'string', 'in:manual,telebirr,bank'],
            'donation_mode' => ['nullable', 'in:one_time,sponsorship'],
            'payment_reference' => ['nullable', 'string', 'max:255'],
            'existing_donation_id' => ['nullable', 'integer', 'exists:donations,id'],
            'name' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['required', 'string', 'max:255'],
            'receipt' => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:2048'],
            'mandate' => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:4096'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ];
    }
}
