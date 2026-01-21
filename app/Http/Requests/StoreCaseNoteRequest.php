<?php

namespace App\Http\Requests;

use App\Models\CaseNote;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreCaseNoteRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create', [CaseNote::class, $this->route('elder')]);
    }

    public function rules(): array
    {
        return [
            'content' => ['required', 'string', 'min:5', 'max:2000'],
            'visibility' => ['required', 'string', Rule::in(['internal', 'branch', 'donor_visible'])],
            'attachments' => ['nullable', 'array', 'max:5'],
            'attachments.*' => ['file', 'max:5120'],
        ];
    }

    public function messages(): array
    {
        return [
            'content.required' => 'The note content is required.',
            'content.min' => 'The note must be at least :min characters.',
            'content.max' => 'The note may not be greater than :max characters.',
            'visibility.in' => 'The selected visibility is invalid.',
        ];
    }
}
