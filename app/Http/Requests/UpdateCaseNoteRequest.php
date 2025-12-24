<?php

namespace App\Http\Requests;

use App\Models\CaseNote;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateCaseNoteRequest extends FormRequest
{
    private CaseNote $caseNote;

    protected function prepareForValidation()
    {
        $this->caseNote = $this->route('caseNote');
    }

    public function authorize(): bool
    {
        return $this->user()->can('update', $this->caseNote);
    }

    public function rules(): array
    {
        return [
            'content' => ['sometimes', 'required', 'string', 'min:5', 'max:2000'],
            'visibility' => ['sometimes', 'required', 'string', Rule::in(['internal', 'donor_visible'])],
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
