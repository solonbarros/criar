<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Request validation for creating visitors.
 */
class StoreVisitorRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('visitors.manage') ?? false;
    }

    public function rules(): array
    {
        return [
            'full_name' => ['required', 'string', 'max:255'],
            'document_number' => ['required', 'string', 'max:50', 'unique:visitors,document_number'],
            'email' => ['nullable', 'email'],
            'phone' => ['nullable', 'string', 'max:30'],
            'photo_base64' => ['required', 'string'],
            'consent' => ['required', 'boolean'],
        ];
    }
}
