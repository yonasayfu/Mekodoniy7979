<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreVisitRequest extends FormRequest
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
            'user_id' => ['required', 'exists:users,id'],
            'elder_id' => ['required', 'exists:elders,id'],
            'branch_id' => ['required', 'exists:branches,id'],
            'visit_date' => ['required', 'date'],
            'purpose' => ['required', 'string', 'max:255'],
            'notes' => ['nullable', 'string'],
            'status' => ['required', 'string', 'in:pending,approved,rejected,completed'],
            'needs_translator' => ['sometimes', 'boolean'],
            'translator_language' => ['nullable', 'string', 'max:120', 'required_if:needs_translator,true'],
            'needs_transport' => ['sometimes', 'boolean'],
            'transport_preference' => ['nullable', 'string', 'max:120'],
            'logistics_notes' => ['nullable', 'string', 'max:1000'],
            'approved_by' => ['nullable', 'exists:users,id'],
        ];
    }
}
