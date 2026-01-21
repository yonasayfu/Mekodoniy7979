<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UnmatchSponsorshipRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('sponsorships.manage') ?? false;
    }

    public function rules(): array
    {
        return [
            'reason' => ['nullable', 'string', 'max:500'],
        ];
    }
}
