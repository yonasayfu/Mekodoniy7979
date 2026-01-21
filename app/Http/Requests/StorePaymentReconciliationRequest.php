<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePaymentReconciliationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('donations.manage') ?? false;
    }

    public function rules(): array
    {
        return [
            'gateway' => ['required', 'string', 'in:telebirr,cbe'],
            'file' => ['required', 'file', 'mimes:csv,txt'],
            'branch_id' => ['nullable', 'exists:branches,id'],
        ];
    }
}
