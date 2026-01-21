<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreSponsorshipProposalRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('sponsorships.manage') ?? false;
    }

    public function rules(): array
    {
        return [
            'donor_id' => ['required', 'exists:users,id'],
            'relationship_type' => ['nullable', Rule::in(['father', 'mother', 'brother', 'sister'])],
            'amount' => ['required', 'numeric', 'min:100'],
            'frequency' => ['required', Rule::in(['monthly', 'quarterly', 'annually'])],
            'notes' => ['nullable', 'string', 'max:1000'],
            'expires_in_hours' => ['nullable', 'integer', 'min:1', 'max:168'],
        ];
    }
}
