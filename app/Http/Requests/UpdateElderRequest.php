<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateElderRequest extends FormRequest
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
            'branch_id' => ['required', 'exists:branches,id'],
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'date_of_birth' => ['nullable', 'date'],
            'gender' => ['nullable', 'string', 'in:male,female,other'],
            'address' => ['nullable', 'string', 'max:255'],
            'city' => ['nullable', 'string', 'max:255'],
            'country' => ['nullable', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:255'],
                        'bio' => ['nullable', 'string'],
                        'profile_picture' => ['nullable', 'image', 'max:2048'], // Max 2MB, for file upload
                        'priority_level' => ['required', 'string', 'in:low,medium,high'],
                        'health_status' => ['nullable', 'string'],
                        'special_needs' => ['nullable', 'string'],
                        'monthly_expenses' => ['nullable', 'numeric'],
                        'video' => ['nullable', 'file', 'mimetypes:video/*', 'max:102400'], // Max 100MB video file
                    ];
                }
            }
