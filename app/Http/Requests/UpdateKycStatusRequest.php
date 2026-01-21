<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateKycStatusRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('donations.manage') ?? false;
    }

    public function rules(): array
    {
        return [
            'status' => ['required', 'in:not_required,pending,verified,rejected'],
            'notes' => ['nullable', 'string', 'max:500'],
            'document' => ['nullable', 'file', 'mimetypes:application/pdf,image/*', 'max:10240'],
        ];
    }
}
