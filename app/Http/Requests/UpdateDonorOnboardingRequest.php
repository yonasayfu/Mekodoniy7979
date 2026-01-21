<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateDonorOnboardingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->hasAnyRole(['External', 'Donor']) ?? false;
    }

    public function rules(): array
    {
        $complete = $this->boolean('complete');
        $required = $complete ? 'required' : 'nullable';

        return [
            'relationship_goal' => [$required, Rule::in(['father', 'mother', 'brother', 'sister', 'open'])],
            'monthly_budget' => [$required, 'numeric', 'min:100'],
            'frequency' => ['required', Rule::in(['monthly', 'quarterly', 'annually', 'once'])],
            'preferred_contact_method' => [$required, 'string', 'max:50'],
            'contact_channels' => ['nullable', 'array'],
            'contact_channels.*' => ['string', 'max:50'],
            'payment_preference' => [$required, Rule::in(['telebirr_auto', 'cbe_auto', 'manual'])],
            'notes' => ['nullable', 'string', 'max:1000'],
            'onboarding_step' => ['required', 'string', 'max:50'],
            'complete' => ['nullable', 'boolean'],
        ];
    }
}
