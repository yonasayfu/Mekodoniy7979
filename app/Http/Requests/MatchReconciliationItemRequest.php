<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MatchReconciliationItemRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('donations.manage') ?? false;
    }

    public function rules(): array
    {
        return [
            'donation_reference' => ['required', 'string', 'max:255'],
        ];
    }
}
