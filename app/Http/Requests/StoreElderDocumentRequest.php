<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreElderDocumentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('elders.manage') ?? false;
    }

    public function rules(): array
    {
        return [
            'type' => ['required', 'string', 'in:consent,medical_report,id_document,other'],
            'label' => ['nullable', 'string', 'max:255'],
            'file' => ['required', 'file', 'max:5120'],
        ];
    }
}
